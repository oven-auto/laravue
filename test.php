<?php

//Интерфейс что бы у всех моделей был метод приведения к строке
interface Stringify
{
    public function toString() : string;
}



/**
 * Класс сингелтон для хранения подключения к бд
 */
Class Connection
{
    public $db;



    private function __construct()
    {
        
    }



    public function init()
    {
        if(self::$db!=NULL){
			return self::$db;
		}

		self::$db = new \PDO("mysql:host=test;dbname=test,test,test");
		self::$db->query('SET CHARSET UTF8');
		return self::$db;
    }
}



/**
 * Ну собствнно наша модель как бы
 */
class Order implements Stringify{
    protected $clientId;
    protected $productId;
    protected $comment;

    public function __construct(array $arr)
    {
        $this->productId        = $arr[0];
        $this->clientId         = $arr[1];
        $this->comment          = $arr[2];
    }



    /**
     * вернуть ввиде ассоциативного массива
     */
    public function getData() : array
    {
        return [
            'productId' => $this->productId,
            'clientId' => $this->clientId,
            'comment' => $this->comment,
        ];
    }



    /**
     * Привести к строке
     */
    public function toString(): string
    {
        return join(';', $this->getData());
    }



    /**
     * Ну допустим есть какие-то правила валидации, не лучший выбор делать это в самой модели ибо не солид, но всё же
     */
    public function validate() : bool
    {
        $res = !is_numeric($this->productId) || !is_numeric($this->clientId) || !is_string($this->comment);

        return $res ? 0 : 1;
    }



    /**
     * Метод для записи данных в бд, само собой это примитив, ибо для этого есть ормки
     */
    public function insert() : void
    {
        $db = Connection::init(); //получаем конект к бд
        //строим запросик и биндим аргументы
        $query = "INSERT INTO orders (item_id,customer_id,comment) VALUES (:productId, :clientId, :comment)";
        //исполняем запрос
        $st = $db->prepare($query);
        $st->execute($this->getData());
    }
}



/**
 * Какой то класс содержащий логику сохранения не валидированных жанных
 */
Class LogFile
{
    public static function write(Stringify $order) : void
    {
        $file = 'error.txt';
        
        file_put_contents($file, $order->toString(), FILE_APPEND | LOCK_EX);
    }
}



/**
 * собственно клиентсикй код
 */
function client_code()
{
    $file = fopen('message.txt', 'r');//открываем фаил

    while (($line = fgets($file)) !== false) {//пока не конец почторчно читаем
        $data = explode(';', $line);//из строки получаем массив через рзделитель

        $order = new Order($data); // как бы создаем новую модель

        if($order->validate()) //пытаемся валидировать
            $order->insert();//если валидно заносим в бд
        else //иначе
            LogFile::write($order);//записываем в нечто подобное лога
    }
}


a. 
SELECT c.name from clients c
LEFT JOIN orders o on o.customer_id = c.id
WHERE date(o.order_date) < date(date_sub(now(), interval 7 day))

b.
SELECT c.id, count(o.id) as o_count FROM clients c
LEFT JOIN orders o on o.client_id = c.id 
group by c.id 
order BY m_count DESC
LIMIT 5

c. ОЧЕВИДНО СТОИМОСТЬ ХРАНИТСЯ В САМОМ ЗАКАЗЕ ДОПУСТИМ ЭТО CТОЛБЕЦ price
SELECT c.id, sum(o.price) as o_sum FROM clients c
LEFT JOIN orders o on o.client_id = c.id 
group by c.id 
order BY m_count DESC
LIMIT 10

d.
SELECT m.name from merchandise m 
LEFT JOIN orders o on o.item_id = m.id
GROUP BY m.id 
HAVING SUM(IF(o.status = 'complete', 1, 0)) = 0