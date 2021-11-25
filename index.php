<?
require __DIR__ . "/vendor/autoload.php";
require_once("vendor/fabpot/goutte/Goutte/Client.php");

use Goutte\Client;

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
$ch = curl_init();
if (isset($_POST["txtInput"]) && $_POST["txtInput"] != "") {
    ini_set('max_execution_time', '8000');

    $client = new Client();
    $postField = htmlspecialchars(trim($_POST["txtInput"]));

    //get csrf token
    $csrfUrl = "https://search.ipaustralia.gov.au/trademarks/search/advanced";

    $responseHeaders = getResponseHeaders($csrfUrl, $ch);

    $csrfToken = fetchCsrfTokenFromHeaders($responseHeaders);


    //Get redirect url from post url
    $postUrl = "https://search.ipaustralia.gov.au/trademarks/search/doSearch";
    $getUrl = getRedirectionUrl($postUrl, $csrfToken, $postField, $ch);
    $resultUrl = "https://search.ipaustralia.gov.au/trademarks/search/result" . substr($getUrl, strpos($getUrl, '?s='));
    $crawler1 = $client->request('GET', $resultUrl);

    //Get total result from parser html.
    $count = $crawler1->filter('h2')->first();
    if ($count->count() > 0) {
        $totalres = $count->text();
    } else $totalres = 0;

    //for each element we get result
    if ($totalres > 0) {
        echo 'Total result is: <b>' . $totalres . '</b>';
        $ceilVal = ceil(str_replace(',', '', $totalres) / 100);
        $totalResult = array();
        for ($i = 0; $i < $ceilVal; $i++) {
            $crawler2 = $client->request('GET', $resultUrl . '&p=' . $i);
            $result = $crawler2->filter('.js-mark-record')->each(function ($node) {
                //number
                $number = $node->filter('.qa-tm-number')->first();
                if ($number->count() > 0) $number = $number->text();
                //logo url, some url is null, that's why i used try catch for null exception
                try {
                    $logoPathUrl = $node->filter('img')->attr('src');
                    $logo_url = "<a href=$logoPathUrl target='_blank'>$logoPathUrl</a>";
                } catch (Exception $e) {
                    $logo_url = "null";
                }
                //name
                $name = $node->filter('.trademark')->first();
                if ($name->count() > 0) $name = empty($name->text()) ? "null" : $name->text();
                //class
                $class = $node->filter('.classes')->first();
                if ($class->count() > 0) $class = $class->text();
                //status
                $status = $node->filter('.mark-line .status')->first();
                if ($status->count() > 0) $status = $status->text();
                //page url
                $details_page_url = $node->filter('a')->attr('href');
                $url_source = "https://search.ipaustralia.gov.au" . "" . $details_page_url ?? null;
                $url_source_link = "<a href=$url_source target='_blank'>$url_source</a>";
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
            $totalResult = array_merge($totalResult, $result);
        }
        echo "<pre>";
        echo json_encode($totalResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        echo "</pre>";
    } else {
        $res = [
            'message' => $crawler1->filter('.no-content p')->first()->text()
        ];
        echo "<pre>";
        echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "</pre>";
        return;
    }
} else {
    echo "Please fill out this field";
    exit();
}

//get headers from url
function getResponseHeaders($url, $ch)
{
    curl_setopt($ch, CURLOPT_URL, $url);
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
    $tmpfname = getcwd() . "\cookies.txt";
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpfname);
    $resp = curl_exec($ch);
    return $headers;
}

function fetchCsrfTokenFromHeaders($headers)
{
    if (!isset($headers['set-cookie'])) return "";

    $searchedKey = "XSRF-TOKEN";

    foreach ($headers['set-cookie'] as $key => $cookie) {
        if (strlen($cookie) >= strlen($searchedKey) && substr($cookie, 0, strlen($searchedKey)) == $searchedKey) {

            $startIndex = strpos($cookie, "=");
            $stopIndex = strpos($cookie, ";");

            if ($startIndex === false || $stopIndex === false) return "";

            return substr($cookie, $startIndex + 1, $stopIndex - $startIndex - 1);
        }
    }
    return "";
}

//get redirection url
function getRedirectionUrl($url, $csrfToken, $postField, $ch)
{
    $data_array = array(
        '_csrf' => $csrfToken,
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
    ];

    $data = http_build_query($data_array);
    $cookiePath = getcwd() . "\cookies.txt";
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
    return $getUrl;
}

?>