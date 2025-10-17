<html>

<head>
    {{--  <link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" /> --}}
    <link href="https://fonts.googleapis.com/css?family=Quantico" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Coda" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed" rel="stylesheet">
    <style>
        body {
            background: rgb(204, 204, 204);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";

        }

        page {
            background: white;
            display: block;
            margin: 0 auto;
            margin-bottom: 0.5cm;
            /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);*/
        }

        page[size="A6"] {
            width: 10.5cm;
            height: 14.8cm;
        }

        @media print {

            body,
            page {
                margin: 0;
                box-shadow: 0;
            }
        }

        .column {
            float: left;
            padding: 5px;
        }

        /* Clear floats after image containers */
        .row::after {
            content: "";
            clear: both;
            display: table;
        }

        .columndiv {}

        .rowdiv:after {
            content: "";
            display: table;
            clear: both;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table th {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            font-family: 'Roboto Condensed', sans-serif;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 80%;
        }

        .outerDiv {

            width: 100%;
            margin: 0px auto;
            padding: 5px;
        }

        .leftDiv {
            color: #000;
            width: 56%;
            float: left;
        }

        .rightDiv {
            color: #000;
            width: 40%;
            float: right;
            text-align: right;
        }
    </style>
</head>

<body>
    <page size="A6">
        <div class="content-body main-page" style="padding:1rem;">
            <div class="row sub-page">

                <div style="height: 50px;background-color: #f5f5f5; margin-bottom: 10px;">
                    <div style="padding-top:5px">
                        <div class="col-12 py-2">
                            <img src="{{ asset('assets/img/logo/jti-logo.png') }}" height="35px" class="center"
                                style="text-align: center;" />
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <p style="text-align: center; margin: 13px 30px 20px 30px; padding-top:5px;" font-size:19px;">
                        <strong>{{ $submission->title }}</strong>
                    </p>
                </div>

                <div class="outerDiv">
                    <div class="leftDiv">
                        <p style="margin: 5px 0px 0px 0px; font-family: 'Roboto Condensed', sans-serif;">Di Isi Oleh:
                        </p>
                        <p style="margin: 0px 0px 0px 0px; font-size:17px;"><strong>{{ $user->name }}</strong></p>
                        {{-- <p style="margin: 5px 0px 0px 0px; font-family: 'Roboto Condensed', sans-serif;">Semester:</p>
                                <p style="font-size: 13px;margin: 0px 0px 0px 0px"><strong> {{$dataSemester->semesterData->semester}} ({{$dataSemester->semesterData->is_genap?"Genap":"Ganjil"}}) {{$dataSemester->semesterData->tahun_ajaran}} </strong> </p> --}}
                        <p style="margin: 5px 0px 0px 0px; font-family: 'Roboto Condensed', sans-serif;">Program Studi:
                        </p>
                        <p style="margin: 0px 0px 0px 0px; font-size:13px;">
                            <strong>{{ $user->details['student_detail']['study_program_name'] }}</strong>
                        </p>
                    </div>

                    <div class="rightDiv">
                        <p style="margin: 5px 0px 0px 0px;font-family: 'Roboto Condensed', sans-serif;"> NIM : </p>
                        <p style="margin: 0px 0px 0px 0px;font-size: 15px;">
                            <strong>{{ $user->details['student_detail']['nim'] }}</strong>
                        </p>
                        <p style="margin: 5px 0px 0px 0px;font-family: 'Roboto Condensed', sans-serif;">Pada Tanggal :
                        </p>
                        <p style="margin: 0px 0px 0px 0px;font-size: 13px;">
                            <strong>{{ $submission->created_at }}</strong>
                        </p>
                    </div>

                    <div style="clear: both;"></div>
                </div>
                <div class="clearfix"></div>
                <div class="mb-2">
                    <p style="text-align: center; margin: 30px 0px 0px 0px; padding-top:5px;"> {{ $qrCode }} </p>
                    <p style="text-align: center; margin: 0;"> {{ $submission->form->code }}</p>
                </div>
            </div>
            <br>
        </div>
        </div>
        </div>
    </page>
</body>

</html>
