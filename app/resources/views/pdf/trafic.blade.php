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
    h2{
        text-align: center;
        font-size:22px;
        margin-bottom:0px;
    }
    body{
        /* background:#f2f2f2; */
        font-family: "DejaVu Sans" !important;
    }
    .section{
        margin-top:30px;
        padding:50px;
        background:#fff;
    }
    .pdf-btn{
        margin-top:30px;
    }
</style>
<body>
    <div class="container">
        <div class="col-md-12 p-2">
            <div class="">
                <div class="">

                </div>
                <div class="panel-body">
                    <div class="main-div">
                        <table class="table">
                            <tr>
                                <td>
                                    <h2>Обращение </h2>
                                </td>
                                <td>
                                    <h2>№ {{$trafic->id}}</h2>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Автор: {{$trafic->author->cut_name}}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="text-center pdf-btn">
                  <a href="#" class="btn btn-primary">Generate PDF</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
