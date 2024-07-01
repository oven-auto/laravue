TELEGRAM

Модуль для телеграм бота и отпраки сообщений клиентам

Под капотом библиотека vendor/irazasyed/telegram-bot-sdk

Класс Telegram является оберткой для библиотеки vendor/irazasyed/telegram-bot-sdk
В данный момент это сингелтон. Конструктор запривачен. Класс инициализируется через статичный метод init.
В этом методе устанавливается API:KEY телеграма и тк в данный момент работает на лонг пулинг устанавливается последний офсет.

Для получения сообщений импользуется метод getUpdates, он получит новые сообщения и установит и сохранит новый офсет.

Для сохранения последнего индекса пулинга используется таблица telegram_longpolling_offsets, ее модель App\Models\TelegramLongpollingOffset.
При каждом считывании данных из телеграмм, в таблицу telegram_longpolling_offsets в поле value записывается последний офсет сообщений.



1 ТЕЛЕГРАМ БОТ

1.1 За обработку сценариев отвечает класс Scenario

Обработка начнется после вызова метода обработчика handler
Данный метод получит массив сообщений через лонгпулинг
после чего пробежится по всем сообщениям из массива
На каждой итеррации проверяется есть ли в сообщение содержимое текст и callback
Проверка нивилирует ошибки тк перед проверкой стоит знак @
После заполняются параметры text, chatId, userId

Запускается фабрика сцен, фабрика возвращает сцену в зависимости от того что было передано в text
1 - Получение и запуск новой сцены, если первый символ сообщения /. Сохранится конект пользователя в таблице telegram_conection, куда запишутся
    chat_id, user_id, last_command, state
2 - Получение сцены, команда которой была последней, те если до этого сообщения была команда /help, то идущее после него сооьщение не начинающееся на /
    будет считаться шагом команды /help
3 - Сцена ошибки

1.2 СЦЕНЫ

Общий смысл - глубина и как можно меньше логики внутри сценариев
Каждая сцена должна являться дочерней к AbstractScene и реализовывать интерфейс SceneInterface

Свойства
- public $options - опции которые будут передаваться в сообщение (поддержка html, отмена превью)
- public $stateCount - кол-во шагов
- public $answer - ответы бота, сколько шагов столько ответов
- public $storageState - параметры которые будут попадать в хранилище, индекс равен текущему шагу, поэтому задавать не с 0, а минимум с 1
- public $rules - правила которые будут проверяться перед тем как на шаге будут добавляться данные в хранилище

Методы
- bysinesLogic метод который будет запущен на последнем шаге, именно в нем и делать всю логику после сбора всех данных каждого шага

Допустим в сценарии регистрации указано 3 ответа и 2 параметра для хранилища, кол-во шагов 3, тк 1шаг это просто ответ бота, 2шаг это сохранения первого параметра и
ответ бота на шаге 2, шаг 3 это ответ бота и сохранение второго параметра, и далее тк как текущий шаг = 3, а всего шагов 3, то запустится метод bysinesLogic в котором можно делать логику.

Все свойства можно переопределить в конструкторе не забыв первой же командой указать parent::_construct()
Переопределение может понадобиться если ответы бота динамические, зависящие от того что ввел пользователь в бота.

Все доступные правила находятся в Классе Rules



2 УВЕДОМЛЕНИЯ

Обработчиком всех уведомлений является класс TelegramNotice, запускается статичным методом run в который нужно передать объект сущности данные которой будут переданы в уведомлении. В результате фабрика создаст обработчик под переданную модель. Но название модели должно совпадать с первой частью названия уведомления. Например если в метод run передать объект модели \App\Models\Trafic то и класс уведомления должен иметь название TraficNotice. При вызове run, вернется сам объект TelegramNotice, в котором сохранено состояние обработчика. Далее используя __сall можно вызывать методы самого уведомления. Например в TraficNotice есть 2 метода, это waiting (уведомление для ожидающего трафика) и assign(Уведомление для назначеного трафика). В методе __call класса TelegramNotice мы пробуем обратиться к методам обработчика который зафиксирован в классе TelegramNotice, если указаны параметры то они подставятся как массив. После можно вызвать метод send, в который необходимо передать массив ид пользователей которым надо отправить уведомление.

TelegramNotice::run((new \App\Trafic\Model::first()))->waiting($name)->send([1,2,3])

