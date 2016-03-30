<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css" />
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>


    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
            integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
            crossorigin="anonymous"></script>

    <script type="text/javascript" src="tipped.js"></script>
    <link rel="stylesheet" type="text/css" href="tipped.css"/>


</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Poloniex Dashboard</a>
            <form class="form-inline navbar-form navbar-right">
                <div class="form-group">
                    <label class="sr-only" for="apikey">Api Key</label>
                    <input type="text" class="form-control" id="apikey" placeholder="API Key">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="apisecretkey">Api Secret Key</label>
                    <input type="text" class="form-control" id="apisecretkey" placeholder="API Secret Key">
                </div>

            </form>
        </div>
    </div>
</nav>
<div class="container">


    <table class="table table-hover">
        <thead>
        <tr>
            <th>Coin</th>
            <th>Current price</th>
            <th>Average price paid</th>
            <th class="pull-right">Amount</th>
            <th></th>
            <th>Total worth</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Huidige waarde:</strong><br><small>Inclusief losse bitcoins</small></td>
            <td id="totalWorth"></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Geïnvesteerd:</strong></td>
            <td id="amountDeposited"></td>
        </tr>
        </tfoot>
        <tr style="display: none;">
            <td id="coin"></td>
            <td id="price"></td>
            <td id="pricePaid"></td>
            <td id="amount"></td>
            <td id="pricePaidWorth"></td>
            <td id="worth"></td>
        </tr>

    </table>
    <span id="updated" style="font-size: 10px"></span>

</div>
<script src="update.js"></script>

</body>
</html>

