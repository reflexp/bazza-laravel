<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ClientDialogs;
use App\Models\ClientDialogMessages;
use App\Models\Files;
use App\Models\Clients;
use App\Models\Users;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function indexPage() {
        return view('Admin.pages.chat.index');
    }

    public function getNewMessages(Request $request) {
        // Берем данные пользователя
        $user = $request->attributes->get('adminInfo');
        // Библиотека времени
        $date = new Carbon;
        // Обновляем данные кто в последний раз просмотрел чат и в какое время
        $clientDialog = ClientDialogs::where('id', $request->chatID)->first();
                
        $clientDialog->lastCheckBy = $user->id;
        $clientDialog->lastCheck = $date->today()->format('Y-m-d H:i:s');

        $clientDialog->update();
        // Обновляем данные, что все сообщения на данный момент были прочитаны
        $clientDialogMessages = ClientDialogMessages::where('dialogID', $request->chatID)
            ->where('senderClientID', '!=', null)
            ->orderBy('id', 'asc')     
            ->update(['isRead' => true]);

        $clientDialogMessages = ClientDialogMessages::where('dialogID', $request->chatID)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($clientDialogMessages as $clientDialogMessage) {
            // Если в сообщении присутствует файл, то прописываем путь для него
            if ($clientDialogMessage->messageFileID != null) {
                $file = Files::find($clientDialogMessage->messageFileID);
                
                $file->path = asset($file->path.$file->name);

                $clientDialogMessage->file = $file;
            }

            // Получаем id последнего сообщения
            $clientDialogMessage->lastMessageID = $clientDialogMessages->max('id');

            // Берем информацию об участниках диалога
            $clientDialogMessage->clientInfo = Clients::where('id', $clientDialogMessage->senderClientID)->first(['id', 'name', 'login', 'buyerType']);
            $clientDialogMessage->adminInfo = Users::where('id', $clientDialogMessage->senderManagerID)->first(['name']);
        }

        return $clientDialogMessages;
    }

    public function getDialogs(Request $request) {
        // Извлекаем диалоги по дате
        $chats = ClientDialogs::orderBy('client_dialogs.updated_at', 'desc')
            ->join('clients', 'clients.id', 'client_dialogs.clientID')
            ->select(
                'client_dialogs.*', 
                'clients.name as clientName',
            )
            ->get();

        // Извлекаем данные по прочитанности последних сообщений в диалоге
        foreach ($chats as $chat) {
            $chat->chatInfo = ClientDialogMessages::orderBy('id', 'desc')
                ->where('dialogID', $chat->id)
                ->where('senderClientID', $chat->clientID)
                ->first(['isRead']);
        }

        // Возвращаем данные о диалогах
        return $chats;
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
        $user = $request->attributes->get('adminInfo');
        
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

        // Находим диалог в базе
        $clientDialog = ClientDialogs::where('id', $request->chatID)->first();
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
        $clientDialogMessage->senderClientID = NULL;
        // по умолчанию ни один менеджер еще не привязан, поэтому id пустой
        $clientDialogMessage->senderManagerID = $user->id;
        // Сохраняем это все в базу
        $clientDialogMessage->save();

        return ['ok' => true, 'result' => []];
    }
}
