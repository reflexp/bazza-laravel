<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ClientDialogs;
use App\Models\ClientDialogMessages;
use App\Models\Files;
use App\Models\Clients;
use Carbon\Carbon;

class ChatController extends Controller
{   
    public function getNewMessages(Request $request) {
        // Берем данные пользователя
        $user = $request->attributes->get('clientInfo');
        // Берем данные о диалоге
        $clientDialogID = ClientDialogs::where('clientID', $user->id)
            ->join('client_dialog_messages', 'client_dialog_messages.dialogID', 'client_dialogs.id')
            ->select('client_dialogs.id', 'client_dialog_messages.senderClientID')
            ->first();
        // Берем данные о сообщениях менеджера
        $clientDialogMessagesAdmin = ClientDialogMessages::where('dialogID', $clientDialogID->id)
            ->where('senderClientID', null)
            ->update(['isRead' => true]);

        $clientDialogMessages = ClientDialogs::where('clientID', $user->id)
            ->join('client_dialog_messages', 'client_dialog_messages.dialogID', 'client_dialogs.id')
            ->select('client_dialogs.*', 'client_dialog_messages.*')
            ->orderBy('client_dialogs.id', 'asc')
            ->get();

        foreach ($clientDialogMessages as $clientDialogMessage) {
            if ($clientDialogMessage->messageFileID != null) {
                $file = Files::find($clientDialogMessage->messageFileID);
                
                $file->path = asset($file->path.$file->name);

                $clientDialogMessage->file = $file;
            }

            // Берем информацию о клиенте
            $clientDialogMessage->clientInfo = Clients::where('id', $clientDialogMessage->senderClientID)->first(['name']);
        }

        return $clientDialogMessages;
    }

    public function addMessage(Request $request) {
        $validateRules = [
            'message' => ['required', 'string', 'min:1'],
            'file' => ['file', 'mimes:jpg,jpeg,png,doc,docx,xls,xlsx,txt', 'max:2000'],
        ];
        // Валидация
        $validation = $request->validate($validateRules);
        // Библиотека времени
        $date = new Carbon;
        // Берем данные пользователя
        $user = $request->attributes->get('clientInfo');
        
        // Берем файл
        $file = $request->file('file');
        // Если есть файл перемещаем его
        if ($file) {
            // Переименовываем файл
            $filename = md5(time()) . md5(time() + 1) . '.' . strtolower($file->getClientOriginalExtension());
            // Настраиваем путь для файла
            $location = public_path('files\chat\\'.$date->year.'\\'.$date->month);
            // Перемещаем файл
            $file->move($location, $filename);
            // Записываем в базу данных файл, который был успешно перемещен
            $file = new Files;

            $file->path = 'files/chat/'.$date->year.'/'.$date->month.'/';
            $file->name = $filename;
            $file->type = 2;

            $file->createdBy = $user->id;
            
            $file->save();
        }

        // Ищем существует ли диалог в базе
        $clientDialog = ClientDialogs::where('clientID', $user->id)->first();
        // Если не существует, добавляем первое упоминание о нем
        if (!$clientDialog) {
            $clientDialog = new ClientDialogs;

            $clientDialog->clientID = $user->id;
            $clientDialog->lastCheck = $date->today()->format('Y-m-d H:i:s');

            $clientDialog->save();
        }

        // Создаем новое сообщение
        $clientDialogMessage = new ClientDialogMessages;
        // Прописываем id диалога
        $clientDialogMessage->dialogID = $clientDialog->id;
        // Вводим сообщение пользователя
        $clientDialogMessage->messageText = $request->message;
        // По умолчанию сообщение не прочитано
        $clientDialogMessage->isRead = false;
        // Если файл был прикреплен, то $file будет true и приравниваем id файла в базе, если его не было то NULL
        if ($file) 
            $clientDialogMessage->messageFileID = $file->id;
        else 
            $clientDialogMessage->messageFileID = NULL;
        // id клиента, который отправил сообщение
        $clientDialogMessage->senderClientID = $user->id;
        // по умолчанию ни один менеджер еще не привязан, поэтому id пустой
        $clientDialogMessage->senderManagerID = NULL;
        // Сохраняем это все в базу
        $clientDialogMessage->save();

        return ['ok' => true, 'result' => []];
    }
}
