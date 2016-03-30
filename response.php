<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// FINAL TESTED CODE - Created by Compcentral

// NOTE: currency pairs are reverse of what most exchanges use...
//       For instance, instead of XPM_BTC, use BTC_XPM

class poloniex
{
    protected $api_key;
    protected $api_secret;
    protected $trading_url = "https://poloniex.com/tradingApi";
    protected $public_url = "https://poloniex.com/public";

    public function __construct($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    private function query(array $req = array())
    {
// API settings
        $key = $this->api_key;
        $secret = $this->api_secret;

// generate a nonce to avoid problems with 32bit systems
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);

// generate the POST data string
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $secret);

// generate the extra headers
        $headers = array(
            'Key: ' . $key,
            'Sign: ' . $sign,
        );

// curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT,
                'Mozilla/4.0 (compatible; Poloniex PHP bot; ' . php_uname('a') . '; PHP/' . phpversion() . ')'
            );
        }
        curl_setopt($ch, CURLOPT_URL, $this->trading_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

// run the query
        $res = curl_exec($ch);

        if ($res === false) throw new Exception('Curl error: ' . curl_error($ch));
//echo $res;
        $dec = json_decode($res, true);
        if (!$dec) {
//throw new Exception('Invalid data: '.$res);
            return false;
        } else {
            return $dec;
        }
    }

    protected function retrieveJSON($URL)
    {
        $opts = array('http' =>
            array(
                'method' => 'GET',
                'timeout' => 10
            )
        );
        $context = stream_context_create($opts);
        $feed = file_get_contents($URL, false, $context);
        $json = json_decode($feed, true);
        return $json;
    }

    public function get_balances()
    {
        return $this->query(
            array(
                'command' => 'returnBalances'
            )
        );
    }

    public function get_open_orders($pair)
    {
        return $this->query(
            array(
                'command' => 'returnOpenOrders',
                'currencyPair' => strtoupper($pair)
            )
        );
    }

    public function get_my_trade_history($pair)
    {
        return $this->query(
            array(
                'command' => 'returnTradeHistory',
                'currencyPair' => strtoupper($pair)
            )
        );
    }

    public function buy($pair, $rate, $amount)
    {
        return $this->query(
            array(
                'command' => 'buy',
                'currencyPair' => strtoupper($pair),
                'rate' => $rate,
                'amount' => $amount
            )
        );
    }

    public function sell($pair, $rate, $amount)
    {
        return $this->query(
            array(
                'command' => 'sell',
                'currencyPair' => strtoupper($pair),
                'rate' => $rate,
                'amount' => $amount
            )
        );
    }

    public function cancel_order($pair, $order_number)
    {
        return $this->query(
            array(
                'command' => 'cancelOrder',
                'currencyPair' => strtoupper($pair),
                'orderNumber' => $order_number
            )
        );
    }

    public function withdraw($currency, $amount, $address)
    {
        return $this->query(
            array(
                'command' => 'withdraw',
                'currency' => strtoupper($currency),
                'amount' => $amount,
                'address' => $address
            )
        );
    }

    public function get_trade_history($pair)
    {
        $trades = $this->retrieveJSON($this->public_url . '?command=returnTradeHistory&currencyPair=' . strtoupper($pair));
        return $trades;
    }

    public function get_withdrawals_and_deposits()
    {
        return $this->query(
            array(
                'command' => 'returnDepositsWithdrawals',
                'start' => time() - 126144000,
                'end' => time()
            )
        );
    }

    public function get_order_book($pair)
    {
        $orders = $this->retrieveJSON($this->public_url . '?command=returnOrderBook&currencyPair=' . strtoupper($pair));
        return $orders;
    }

    public function get_volume()
    {
        $volume = $this->retrieveJSON($this->public_url . '?command=return24hVolume');
        return $volume;
    }

    public function get_ticker($pair = "ALL")
    {
        $pair = strtoupper($pair);
        $prices = $this->retrieveJSON($this->public_url . '?command=returnTicker');
        if ($pair == "ALL") {
            return $prices;
        } else {
            $pair = strtoupper($pair);
            if (isset($prices[$pair])) {
                return $prices[$pair];
            } else {
                return array();
            }
        }
    }

    public function get_trading_pairs()
    {
        $tickers = $this->retrieveJSON($this->public_url . '?command=returnTicker');
        return array_keys($tickers);
    }

    public function get_total_btc_balance()
    {
        $balances = $this->get_balances();
        $prices = $this->get_ticker();

        $tot_btc = 0;

        foreach ($balances as $coin => $amount) {
            $pair = "BTC_" . strtoupper($coin);

// convert coin balances to btc value
            if ($amount > 0) {
                if ($coin != "BTC") {
                    $tot_btc += $amount * $prices[$pair];
                } else {
                    $tot_btc += $amount;
                }
            }

// process open orders as well
            if ($coin != "BTC") {
                $open_orders = $this->get_open_orders($pair);
                foreach ($open_orders as $order) {
                    if ($order['type'] == 'buy') {
                        $tot_btc += $order['total'];
                    } elseif ($order['type'] == 'sell') {
                        $tot_btc += $order['amount'] * $prices[$pair];
                    }
                }
            }
        }

        return $tot_btc;

    }
}

$api = "B23YHGWQ-WAP5P122-03A71UJB-YXR5NBTP";
$apis = "0bdfd9eeb301c4dce55b395faadbcce00e799e8378b09803e37998b3f867ce2e3f6bce17bb3a5af34e62d65b26f7fc8e4b15e1c94bdcc4fd7141565e09807382";
$totalWorth = 0;

$poloniex = new poloniex($api, $apis);
$balance = $poloniex->get_balances();
$tickers = $poloniex->get_ticker();
$deposited = $poloniex->get_withdrawals_and_deposits();
$amountDeposited = 0;
foreach ($deposited['deposits'] as $key => $value) {
    $amountDeposited += $value['amount'];
}

$btcCoins = $balance["BTC"];
$totalWorth += floatval($btcCoins);

//No BTC nor BTM
unset($balance["BTC"]);
unset($balance["BTM"]);

//Lets get all the details
foreach ($balance as $key => $value) {
    //Disregard the coins which are not owned by the user
    if ($value == "0.00000000") {
        unset($balance[$key]);
    } else {
        //Get the current price
        foreach ($tickers as $keyticker => $ticker) {
            if ($keyticker == "BTC_" . $key) {
                //Calculate total worth
                $worth = floatval($value) * floatval($ticker['last']);
                $totalWorth += $worth;

                $paidPC = getBuyPrice("BTC_" . $key, $poloniex);

                $pricePaidWorth = floatval($value) * $paidPC;
                //Create new array with all the information
                $newArray = ['coin' => $key, 'amount' => $value, 3, 'price' => $ticker['last'], 'worth' => $worth, 8, 'paidPC' => $paidPC, 'pricePaidWorth' => $pricePaidWorth ];
                array_push($balance, $newArray);
                unset($balance[$key]);
            }
        }
    }
}

function getBuyPrice($coin, $poloniex)
{
    $tradeHistory = $poloniex->get_my_trade_history($coin);
    $amount = 0;
    $payed = 0;


    foreach (array_reverse($tradeHistory, true) as $key => $value) {
        if ($value['category'] == 'exchange') {

//            var_dump($value);
//            echo "<br>";
            if ($value['type'] == 'buy') {
                $amount += floatval($value['amount']) - round(floatval($value['amount']) * 0.002, 8);
                $payed += $value['total'];
            } else if ($value['type'] == 'sell') {
                $amount -= floatval($value['amount']);
                $payed -= $value['total'];
                $payed -= round($value['total'] * 0.002, 8);
            }

            if (floatval($amount) < 0.1 && floatval($amount) > -0.1) {
                $payed = 0;
                $amount = 0;
            }

//            echo round($amount, 8);
//            echo "<br>";
//            echo round($payed, 8);
//            echo "<br>";
        }

    }
//    echo "<br><br><br><br><br><br>";
    return $perCoin = $payed / $amount;
}


$addedInformation = ['totalWorth' => $totalWorth, 'amountDeposited' => $amountDeposited];
$array = ['coinsData' => $balance, 'extraData' => $addedInformation];

header('Content-Type: application/json');
echo json_encode($array);
//var_dump($balance);

?>