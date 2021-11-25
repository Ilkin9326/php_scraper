<?
require __DIR__ . "/vendor/autoload.php";
require_once("vendor/fabpot/goutte/Goutte/Client.php");

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
?>
    <style>
        .input_text {
            width: 23%;
            height: 30px;
        }

        .input_div {
            text-align: center;
        }


    </style>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Scrape serps</title>
    </head>
    <body>
    <form action="" method="post">
        <div class="input_div"><input type="text" name="txtInput" value="" data-error="1" maxlength="250"
                                      aria-describedby="error_1"
                                      class="input_text" id="wordSearchTerms1" autofocus="autofocus"
                                      placeholder="Search for words in trade marks, owner names or trade mark numbers">

            <button id="qa-search-submit" ng-class="{disabled: !(vm.resultCount !== null || vm.hasImage)}" type="submit"
                    name="btnSubmit"
                    class="button fill green search-button" title="Search" style="height: 30px">
                <span>Search</span>
            </button>
        </div>
    </form>

    </body>
    </html>


<?

if (isset($_POST["txtInput"]) && $_POST["txtInput"] != "") {
    ini_set('max_execution_time', '500');
    $client = new Client(HttpClient::create(['timeout' => 500]));
    $postField = htmlspecialchars(trim($_POST["txtInput"]));
    $url = "https://search.ipaustralia.gov.au/trademarks/search/doSearch";
    //get csrf token
    $csrfUrl = "https://search.ipaustralia.gov.au/trademarks/search/advanced";
    $csrfToken = getCsrfToken($csrfUrl);
    $_csrf = preg_split('/(=|;)/', $csrfToken['set-cookie'][4], -1, PREG_SPLIT_NO_EMPTY);

    $data_array = array(
        '_csrf' => $_csrf[1],
        'wv[0]' => $postField
    );

    $data = http_build_query($data_array);
    $headers = [
        'X-Apple-Tz: 0',
        'X-Apple-Store-Front: 143444,12',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Encoding: gzip, deflate',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control: no-cache',
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'X-MicrosoftAjax: Delta=true'
        //'cookie: SESSIONTOKEN=eyJhbGciOiJIUzUxMiJ9
        //.eyJqdGkiOiIzNDIwNjk5Ni01YmMzLTQxOGEtODcyZC0xNDNmOGQ5YzM4YTMiLCJzdWIiOiJkNmMzOTcyNC1lODNjLTQ5NDUtYjgxNi02NWE0YjYwZGU2OGMiLCJpYXQiOjE2Mzc4MTQ4MjMsImV4cCI6MTYzNzg0MzYyM30.dnGvG49qhlKzR1cXxHVF83cCov-PTjYjTYMahwbaDKe8380u-Wsew7NFDkvjrXYUqinzrf6ar2dN9t8o2OB8Ow; XSRF-TOKEN=76a8ca47-d7ae-4c60-93e2-5a972fe53f82; nmstat=724ca382-8f27-7ba0-947c-f515e9dbf8ae; _ga=GA1.3.807509456.1637581055; _gid=GA1.3.222486322.1637581055; _gat=1; AWSALB=wkDAzROABhP0PHPMeebkk9SwIrTosZzGT2M9HpnuWkERTa+c7+v7quQHfnN/Nvy7yJqGuB/O3QnAknh8Nl4hYM/KCcEzyBXNGmq+a3Rx8D9ZZgKiCyBR1JSIsbMX; AWSALBCORS=wkDAzROABhP0PHPMeebkk9SwIrTosZzGT2M9HpnuWkERTa+c7+v7quQHfnN/Nvy7yJqGuB/O3QnAknh8Nl4hYM/KCcEzyBXNGmq+a3Rx8D9ZZgKiCyBR1JSIsbMX'
    ];
    $ch = curl_init();
    $data = http_build_query($data_array);
    $cookiePath = getcwd()."\cookies.txt";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiePath);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    $getUrl = $info["url"];
    curl_close($ch);
    //Url goturuldu, Proses bitdi.
    //search page define
    $crawler1 = $client->request('GET', $getUrl);
    //Get total result from parser html.
    $count = $crawler1->filter('h2')->first();
    if ($count->count() > 0) {
        $totalres = $count->text();
    }else $totalres=0;
    //Each element we get result
    //print_r($count);
    if ($totalres > 0) {
        echo 'Total result is: <b>' . $totalres . '</b>';
        $items_list = array();
        $ceilVal = ceil($totalres / 100);
        $totalResult = array();
        for ($i = 0; $i < $ceilVal; $i++) {
            $crawler2 = $client->request('GET', $getUrl . '&p=' . $i);
            $result = $crawler2->filter('.js-mark-record')->each(function ($node) {
                $number = $node->filter('.qa-tm-number')->first();
                if ($number->count() > 0) {
                    $number = $number->text();
                }
                //getting logo url, some url is null, that's why i used try catch for null exception
                try {
                    $logo_url = $node->filter('img')->attr('src');
                } catch (Exception $e) {
                    $logo_url = "src is null";
                }
                $name = $node->filter('.trademark')->first();
                if ($name->count() > 0) {
                    $name = $name->text();
                }
                $class = $node->filter('.classes')->first();
                if ($class->count() > 0) {
                    $class = $class->text();
                }
                $status = $node->filter('.mark-line .status')->first();
                if ($status->count() > 0) {
                    $status = $status->text();
                }
                $details_page_url = $node->filter('a')->attr('href');
                $url_source = "https://search.ipaustralia.gov.au" . "" . $details_page_url ?? null;
                $url_source_link = "<a href='https://search.ipaustralia.gov.au' . $details_page_url>$url_source</a>";

                $items = array(
                    'number' => $number,
                    'logo_url' => $logo_url,
                    'name' => $name,
                    'classes' => $class,
                    'status' => $status,
                    'details_page_url' => $url_source_link
                );
                return $items;
            });
            array_push($totalResult, $result);
        }
        echo "<pre>";
        print_r($totalResult);
    } else {
        echo "0 results";
        exit();
    }
} else {
    echo "Please fill out this field";
    exit();
}

function getCsrfToken($url)
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$headers) {

        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) { // ignore invalid headers
            return $len;
        }
        $name = strtolower(trim($header[0]));
        if (is_array($headers) && !array_key_exists($name, $headers)) {
            $headers[$name] = [trim($header[1])];
        } else {
            $headers[$name][] = trim($header[1]);
        }

        return $len;

    });

    $tmpfname = getcwd()."\cookies.txt";
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpfname);
    $resp = curl_exec($ch);

    return $headers;

}

?>