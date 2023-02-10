<!DOCTYPE html>
<html>
<head>
    <title>Обращение №</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<style type="text/css">
    @font-face {
        /* font-family: "DejaVu Sans";
        font-style: normal;
        font-weight: 400;
        src: url("/fonts/dejavu-sans/DejaVuSans.ttf"); */
    }
    body{
        /* background:#f2f2f2; */
        font-family: "DejaVu Sans" !important;
    }
    .client h2{
        font-weight: normal;
        font-style: italic;
        margin: 0px;
        padding: 0px;
    }
    .client .client-name{
        font-weight: bold;
    }
    .client .client-phone{
        font-style: normal;
    }
    hr{
        color: #333;
        background: #333;
    }
    .alert-block{
        position: absolute;
        right: 60px;
        top: 150px;
        display:inline-block;
        border: 3px solid red;
        font-size: 30px;
        padding:5px;
    }
</style>
<body>
    <div class="">
        <div class="">
            <h1>Обращение {{$trafic->id}}</h1>
        </div>
    </div>

    <hr>

    <div>
        <div>
            Регистрация: {{$trafic->end_at->format('d.m.Y (H:i)')}}
        </div>

        <div>
            Автор: {{$trafic->author->cut_name}}
        </div>

        <div>
            Статус: {{$trafic->status->description}}
        </div>

        <div>
            Обработано: {{$trafic->processing_at ? 'Да' : 'Нет'}}
        </div>
    </div>

    <div class="alert-block">
        @if( date("d.m.Y H:i", strtotime("+".$trafic->interval." minutes")) > $trafic->end_at->format('d.m.Y H:i') )
            ПРОСРОЧЕНО
        @endif
    </div>

    <div class="pt-3">
        <div><b>Структура обращения</b></div>
        <div>{{$trafic->chanel->name}}</div>
        <div>
            {{$trafic->salon->name}} ●
            {{$trafic->structure->name}} ●
            {{$trafic->appeal->name}}
        </div>
        <div>
            @if($trafic->needs)
                @foreach($trafic->needs as $itemNeed)
                    @if (!$loop->first)
                        ●
                    @endif
                    <span>{{$itemNeed->name}}</span>
                @endforeach
            @else
                <i>Товары/услуги не выбраны</i>
            @endif
        </div>
    </div>

    <div class="pt-3">
        <div><b>Назначенное действие:</b></div>
        <div>
            {{$trafic->task->name}} ●
            {{$trafic->interval}} мин. ●
            {{$trafic->begin_at->format('d.m.Y (H:i)')}} - {{$trafic->end_at->format('d.m.Y (H:i)')}}
        </div>
        <div>
            Ответственный: {{$trafic->manager->cut_name}}
        </div>
    </div>

    <div class="pt-5 client">
        <div>
            <h2 class="client-name">{{$trafic->lastname}} {{$trafic->firstname}}</h2>
        </div>

        <div>
            <h2 class="client-phone">
                @if($trafic->phone)
                    {{$trafic->formated_phone}}
                @else
                    Нет номера телефона
                @endif
            </h2>
        </div>

        <div>
            <h2>
                @if($trafic->email)
                    {{$trafic->email}}
                @else
                    <i>Нет адреса электронной почты</i>
                @endif
            </h2>
        </div>

        <div>
            <h2>
                @if($trafic->zone->id)
                    {{$trafic->zone->name}}
                @else
                    <i>Зона контакта неизвестна</i>
                @endif
            </h2>
        </div>

        <div>
            <h2>
            @if($trafic->comment)
                {{$trafic->coment}}
            @else
                <i>Нет комментария</i>
            @endif
            </h2>
        </div>
    </div>

    <hr>

</body>
</html>
