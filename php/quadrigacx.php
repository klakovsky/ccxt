<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception as Exception; // a common import

class quadrigacx extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'quadrigacx',
            'name' => 'QuadrigaCX',
            'countries' => array ( 'CA' ),
            'rateLimit' => 1000,
            'version' => 'v2',
            'has' => array (
                'fetchDepositAddress' => true,
                'fetchTickers' => true,
                'CORS' => true,
                'withdraw' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766825-98a6d0de-5ee7-11e7-9fa4-38e11a2c6f52.jpg',
                'api' => 'https://api.quadrigacx.com',
                'www' => 'https://www.quadrigacx.com',
                'doc' => 'https://www.quadrigacx.com/api_info',
                'referral' => 'https://www.quadrigacx.com/?ref=laiqgbp6juewva44finhtmrk',
            ),
            'requiredCredentials' => array (
                'apiKey' => true,
                'secret' => true,
                'uid' => true,
            ),
            'api' => array (
                'public' => array (
                    'get' => array (
                        'order_book',
                        'ticker',
                        'transactions',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'balance',
                        'bitcoin_deposit_address',
                        'bitcoin_withdrawal',
                        'bitcoincash_deposit_address',
                        'bitcoincash_withdrawal',
                        'bitcoingold_deposit_address',
                        'bitcoingold_withdrawal',
                        'buy',
                        'cancel_order',
                        'ether_deposit_address',
                        'ether_withdrawal',
                        'litecoin_deposit_address',
                        'litecoin_withdrawal',
                        'lookup_order',
                        'open_orders',
                        'sell',
                        'user_transactions',
                    ),
                ),
            ),
            'markets' => array (
                'BTC/CAD' => array ( 'id' => 'btc_cad', 'symbol' => 'BTC/CAD', 'base' => 'BTC', 'quote' => 'CAD', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTC/USD' => array ( 'id' => 'btc_usd', 'symbol' => 'BTC/USD', 'base' => 'BTC', 'quote' => 'USD', 'maker' => 0.005, 'taker' => 0.005 ),
                'ETH/BTC' => array ( 'id' => 'eth_btc', 'symbol' => 'ETH/BTC', 'base' => 'ETH', 'quote' => 'BTC', 'maker' => 0.002, 'taker' => 0.002 ),
                'ETH/CAD' => array ( 'id' => 'eth_cad', 'symbol' => 'ETH/CAD', 'base' => 'ETH', 'quote' => 'CAD', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/CAD' => array ( 'id' => 'ltc_cad', 'symbol' => 'LTC/CAD', 'base' => 'LTC', 'quote' => 'CAD', 'maker' => 0.005, 'taker' => 0.005 ),
                'LTC/BTC' => array ( 'id' => 'ltc_btc', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'maker' => 0.005, 'taker' => 0.005 ),
                'BCH/CAD' => array ( 'id' => 'bch_cad', 'symbol' => 'BCH/CAD', 'base' => 'BCH', 'quote' => 'CAD', 'maker' => 0.005, 'taker' => 0.005 ),
                'BCH/BTC' => array ( 'id' => 'bch_btc', 'symbol' => 'BCH/BTC', 'base' => 'BCH', 'quote' => 'BTC', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTG/CAD' => array ( 'id' => 'btg_cad', 'symbol' => 'BTG/CAD', 'base' => 'BTG', 'quote' => 'CAD', 'maker' => 0.005, 'taker' => 0.005 ),
                'BTG/BTC' => array ( 'id' => 'btg_btc', 'symbol' => 'BTG/BTC', 'base' => 'BTG', 'quote' => 'BTC', 'maker' => 0.005, 'taker' => 0.005 ),
            ),
            'exceptions' => array (
                '101' => '\\ccxt\\AuthenticationError',
            ),
        ));
    }

    public function fetch_balance ($params = array ()) {
        $balances = $this->privatePostBalance ();
        $result = array ( 'info' => $balances );
        $currencies = is_array ($this->currencies) ? array_keys ($this->currencies) : array ();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $lowercase = strtolower ($currency);
            $account = array (
                'free' => floatval ($balances[$lowercase . '_available']),
                'used' => floatval ($balances[$lowercase . '_reserved']),
                'total' => floatval ($balances[$lowercase . '_balance']),
            );
            $result[$currency] = $account;
        }
        return $this->parse_balance($result);
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $orderbook = $this->publicGetOrderBook (array_merge (array (
            'book' => $this->market_id($symbol),
        ), $params));
        $timestamp = intval ($orderbook['timestamp']) * 1000;
        return $this->parse_order_book($orderbook, $timestamp);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $response = $this->publicGetTicker (array_merge (array (
            'book' => 'all',
        ), $params));
        $ids = is_array ($response) ? array_keys ($response) : array ();
        $result = array ();
        for ($i = 0; $i < count ($ids); $i++) {
            $id = $ids[$i];
            $symbol = $id;
            $market = null;
            if (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                list ($baseId, $quoteId) = explode ('_', $id);
                $base = strtoupper ($baseId);
                $quote = strtoupper ($quoteId);
                $base = $this->common_currency_code($base);
                $quote = $this->common_currency_code($base);
                $symbol = $base . '/' . $quote;
                $market = array (
                    'symbol' => $symbol,
                );
            }
            $result[$symbol] = $this->parse_ticker($response[$id], $market);
        }
        return $result;
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetTicker (array_merge (array (
            'book' => $market['id'],
        ), $params));
        return $this->parse_ticker($response, $market);
    }

    public function parse_ticker ($ticker, $market = null) {
        $symbol = null;
        if ($market !== null)
            $symbol = $market['symbol'];
        $timestamp = intval ($ticker['timestamp']) * 1000;
        $vwap = $this->safe_float($ticker, 'vwap');
        $baseVolume = $this->safe_float($ticker, 'volume');
        $quoteVolume = $baseVolume * $vwap;
        $last = $this->safe_float($ticker, 'last');
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'high'),
            'low' => $this->safe_float($ticker, 'low'),
            'bid' => $this->safe_float($ticker, 'bid'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'ask'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => null,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
            'info' => $ticker,
        );
    }

    public function parse_trade ($trade, $market) {
        $timestamp = intval ($trade['date']) * 1000;
        return array (
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $market['symbol'],
            'id' => (string) $trade['tid'],
            'order' => null,
            'type' => null,
            'side' => $trade['side'],
            'price' => $this->safe_float($trade, 'price'),
            'amount' => $this->safe_float($trade, 'amount'),
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $market = $this->market ($symbol);
        $response = $this->publicGetTransactions (array_merge (array (
            'book' => $market['id'],
        ), $params));
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $method = 'privatePost' . $this->capitalize ($side);
        $order = array (
            'amount' => $amount,
            'book' => $this->market_id($symbol),
        );
        if ($type === 'limit')
            $order['price'] = $price;
        $response = $this->$method (array_merge ($order, $params));
        return array (
            'info' => $response,
            'id' => (string) $response['id'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        return $this->privatePostCancelOrder (array_merge (array (
            'id' => $id,
        ), $params));
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $method = 'privatePost' . $this->get_currency_name ($code) . 'DepositAddress';
        $response = $this->$method ($params);
        // [E|e]rror
        if (mb_strpos ($response, 'rror') !== false) {
            throw new ExchangeError ($this->id . ' ' . $response);
        }
        $this->check_address($response);
        return array (
            'currency' => $code,
            'address' => $response,
            'tag' => null,
            'info' => $response,
        );
    }

    public function get_currency_name ($code) {
        $currencies = array (
            'ETH' => 'Ether',
            'BTC' => 'Bitcoin',
            'LTC' => 'Litecoin',
            'BCH' => 'Bitcoincash',
            'BTG' => 'Bitcoingold',
        );
        return $currencies[$code];
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->check_address($address);
        $this->load_markets();
        $request = array (
            'amount' => $amount,
            'address' => $address,
        );
        $method = 'privatePost' . $this->get_currency_name ($code) . 'Withdrawal';
        $response = $this->$method (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => null,
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'] . '/' . $this->version . '/' . $path;
        if ($api === 'public') {
            $url .= '?' . $this->urlencode ($params);
        } else {
            $this->check_required_credentials();
            $nonce = $this->nonce ();
            $request = implode ('', array ((string) $nonce, $this->uid, $this->apiKey));
            $signature = $this->hmac ($this->encode ($request), $this->encode ($this->secret));
            $query = array_merge (array (
                'key' => $this->apiKey,
                'nonce' => $nonce,
                'signature' => $signature,
            ), $params);
            $body = $this->json ($query);
            $headers = array (
                'Content-Type' => 'application/json',
            );
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function handle_errors ($statusCode, $statusText, $url, $method, $headers, $body) {
        if (gettype ($body) !== 'string')
            return; // fallback to default $error handler
        if (strlen ($body) < 2)
            return;
        if (($body[0] === '{') || ($body[0] === '[')) {
            $response = json_decode ($body, $as_associative_array = true);
            $error = $this->safe_value($response, 'error');
            if ($error !== null) {
                //
                // array ("$error":{"$code":101,"message":"Invalid API Code or Invalid Signature")}
                //
                $code = $this->safe_string($error, 'code');
                $feedback = $this->id . ' ' . $this->json ($response);
                $exceptions = $this->exceptions;
                if (is_array ($exceptions) && array_key_exists ($code, $exceptions)) {
                    throw new $exceptions[$code] ($feedback);
                } else {
                    throw new ExchangeError ($this->id . ' unknown "$error" value => ' . $this->json ($response));
                }
            }
        }
    }
}
