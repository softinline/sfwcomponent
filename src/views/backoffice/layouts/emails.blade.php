<html>
    <head>
        <style>
            body {
                background-color: #FAFAFA; padding: 80px 0px;
                font-size:14px;
                color:#333;
                font-family: Helvetica
            }
            
            .subject {
                font-size:18px;
                font-weight:bold;
            }
            .hr {
                border:1px solid #ccc;
            }
            .wrapper {
                max-width: 800px;
                margin:auto;
                padding:30px;
                background-color:rgb(255, 255, 255);
            }
            .table-data {
                width:100%;
                border-spacing: 0px;
                border-collapse: collapse;
            }
            .table-data .thead td {
                background-color: #eee;
                height:25px;
                font-weight:bold;
            }
            .table-data tbody tr td {
                height:25px;
                width:10%;
            }
            @media only screen and (max-width: 600px) {
                .row-emobile {
                    height: 100px;
                }
                .wrapper-message {
                    overflow: scroll;
                }
            }
            
            .row-emobile {
                font-size: 14px
            }
            
            a {
                color: blue;
            }

            .table-button {
                font-size: 14px;
                height: auto;
            }

            table, td, th {
                text-align: left;
            }

            th, tr {
                padding: 10px;
            }

            .title {
                background-color: #3399cc;
                color: white;
            }

            tbody tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            .table-title {
                font-size: 28px;
            }

            .btn {
                display: inline-block;
                padding: 6px 12px;
                margin-bottom: 0;
                font-size: 14px;
                font-weight: 400;
                line-height: 1.42857143;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                -ms-touch-action: manipulation;
                touch-action: manipulation;
                cursor: pointer;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                background-image: none;
                border: 0px;
                border-radius: 4px;
                text-decoration: none;
            }

            .btn-primary {
                color: #fff;
                background-color: #337ab7;
                border-color: #2e6da4;
            }

            .btn-lg {
                font-size: 18px;
                line-height: 1.3333333;
                border-radius: 6px;
            }

            .img-responsive {
                display: block;
                max-width: 100%;
                height: auto;
            }

            .budget-table {
                width: 100%;
                font-size: 14px;
                border-collapse: collapse;
                height: auto;
            }
            
            .group-request-table {
                margin-left:auto;
                margin-right:auto;
                font-size: 14px;
                border-collapse: collapse;
                height: auto;
            }
            
        </style>
    </head>
    <body>        
        <div class="wrapper">            
            <div class="wrapper-message">
                <p><span class="subject">@yield('subject')</span></p>
                @yield('message')
                <br>
                @yield('footer')                
            </div>
        </div>
    </body>
</html>
