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
        margin-bottom:50px;
    }
    body{
        background:#f2f2f2;
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
                    <h2>Обращение № {{$id}}</h2>
                </div>
                <div class="panel-body">
                    <div class="main-div">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
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
