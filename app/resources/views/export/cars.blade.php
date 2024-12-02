<table>
    <thead>
    <tr>
        <td colspan="14" size="5" align="center">Экспорт автомобилей</td>
    </tr>

    <tr>
        <th align="center">Марка</th>
        <th align="center">Модель</th>
        <th align="center">№ заказа</th>
        <th align="center">VIN</th>
        <th align="center">Коммерческий цвет</th>
        <th align="center">ТОРГ-12</th>
        <th align="center">Пл.период</th>
        <th align="center">Срок оплаты</th>
        <th align="center">Дата выкупа</th>
        <th align="center">Закуп</th>
        <th align="center">РРЦ</th>
        <th align="center">Опции</th>
        <th align="center">Тюнинг</th>
    </tr>
    </thead>
    <tbody>
    @foreach($trafics as $item)
        <tr>
            <td align="left">{{$item->brand->name}}</td>
            <td align="left">{{$item->mark->name}}</td>
            <td align="left">{{$item->order->order_number}} </td>
            <td align="left">{{$item->vin}} </td>
            <td align="left">{{$item->color->name}}</td>
            <td align="left">{{$item->getInvoiceDate()}}</td>
            <td align="left">{{$item->paid_date->date ?? ''}}</td>
            <td align="left">{{$item->control_paid_date->date ?? ''}}</td>
            <td align="left">{{$item->getRansomDate()}}</td>
            <td align="left">{{$item->purchase->cost ?? ''}}</td>
            <td align="left">{{$item->getComplectationPrice()}}</td>
            <td align="left">{{$item->getOptionPrice()}}</td>
            <td align="left">{{$item->getTuningPrice()}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
