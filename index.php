<?
//bura bele seyler elave edilib xeberin var?
require __DIR__ . "/vendor/autoload.php";
require_once("vendor/fabpot/goutte/Goutte/Client.php");

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
?>
<style>
    .input_text{
        width: 23%;
    }

    .input_div{
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
           class="input_text" id="wordSearchTerms1" autofocus="autofocus" placeholder="Search for words in trade marks, owner names or trade mark numbers">

    <button id="qa-search-submit" ng-class="{disabled: !(vm.resultCount !== null || vm.hasImage)}" type="submit"
            name="btnSubmit"
            class="button fill green search-button" title="Search">
        <span>Search</span>
        </button></div>
</form>

</body>
</html>


<?

//Belli url-e post atib redirect olunan linki goturek.Proses basladi
$data_array = array(
    '_csrf' => 'f9c22ddf-f3ed-4e75-b1c6-3c7024a708f9',
    'wv[0]' => $_POST["txtInput"]

);


$url = "https://search.ipaustralia.gov.au/trademarks/search/doSearch";
$data = http_build_query($data_array);
$headers = [
    'X-Apple-Tz: 0',
    'X-Apple-Store-Front: 143444,12',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Encoding: gzip, deflate',
    'Accept-Language: en-US,en;q=0.5',
    'Cache-Control: no-cache',
    'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    'X-MicrosoftAjax: Delta=true',
    'cookie: SESSIONTOKEN=eyJhbGciOiJIUzUxMiJ9.eyJqdGkiOiI4OGYwNDIxZi00NTQ2LTRlYTMtYjI3Zi1iZTUxZDJmNWNkNWMiLCJzdWIiOiIxMTIwYzRkOS1jYmY4LTRkMTMtYWI3Yi0xMmIwOTNjMjY0N2UiLCJpYXQiOjE2Mzc0MTA3MTUsImV4cCI6MTYzNzQzOTUxNX0.GcEW35gtO7Y64RKOWhU2oQ_PHg0qkrY3wHMSxdjElJpblV-RneSFrAm44MsAGijz-qVKD2LsN7KTX2hm8sS7PA; XSRF-TOKEN=f9c22ddf-f3ed-4e75-b1c6-3c7024a708f9; _gid=GA1.3.1423022112.1637410740; ak_bmsc=CBA1A040ED32C98B5E4F7FE3EFB17EA5~000000000000000000000000000000~YAAQF8MTAr1XPCt9AQAAbd5+PQ0xaAYpt3GXzAS73doiRZyVYLutNh0wO1mvTGHlu+b0pAfilSvTCctaiCRc/mkcCv1vH1y8pMfMku5L+xlU4ghULRPqgyQsIT9u5c26d2eZnDFZCEscoCZkoDkO60VMWfBqjDpI7lL/MrQgZSsHOqj0SqxFJfdtSX+rkMntXvZZvKvNq3wWkSNSMbgCKNaL+UeKm2tWSLhAF0cjE6VT3fuzKOZq37UlcT3Ye1d9AHh8CELA8Qp0nXHyNUbkgB2JFpiZuvk6k8NzUxr3+qbNzMZ2sEBfYE5x2EmPdqLsKqqFXNYGTYgFP6fAJFPNldYPU5CzdOTogw5CQvfoYyVldwHvax/mffU2lFzpG15pnT7HLQQ1gHqeP5eTzN6leSm8zHZkF4TsS3nrl4Ef32WKz1MZYpL4dHFUkDfYsv+GMAq6pKiHM4ZJQLDBvoBqyJGxQnXp4xhUMf3Pd/U6EZzLdugN8WQNaxZ1MgyoBbUGIg==; nmstat=f8b1c345-f295-48b3-e3e7-43a748de6700; _ga=GA1.3.1167855903.1637255945; bm_sv=210EF35B682297A1664B97982C94DAF0~GIHVC2Fw2yEr1tGeAY1nHMzlxrv5+rKKGXzBbHJOScbTdfAAvrsaLgyTtG6BDU1zdebhmG1kBXFeYEoBnfq9a10RJMWyjCNqa6kIS6aDkECEv8EPuK/FPGuMJRi8s3AQYP90QdamOBHgXwzvp9bicNiCa5etJGfYwPKEDbnrAVo=; _ga_QX1TTWEZ1L=GS1.1.1637414260.1.1.1637414336.0; _gat=1; AWSALB=LB/7hqIDTMdqCIMGS7nss2CO92Mc4kDFSqFJOoQn9iCzmx4SEbSlGDhYNS80WKo6dTfeNzkaSJEpUbGJdkFZ2MR7BnaClSErsgtCklo+holW7pNgOnulPYnN0v+D; AWSALBCORS=LB/7hqIDTMdqCIMGS7nss2CO92Mc4kDFSqFJOoQn9iCzmx4SEbSlGDhYNS80WKo6dTfeNzkaSJEpUbGJdkFZ2MR7BnaClSErsgtCklo+holW7pNgOnulPYnN0v+D'
];
$ch = curl_init();
$data = http_build_query($data_array);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);


$resp = curl_exec($ch);
$info = curl_getinfo($ch);
$getUrl = $info["url"];
curl_close($ch);
//Url goturuldu, Proses bitdi.

if (isset($_POST["txtInput"]) && $_POST["txtInput"] != "") {
    $term = $_POST["txtInput"];

    ini_set('max_execution_time', '500');
    $client = new Client(HttpClient::create(['timeout' => 500]));

    //search page define
    $crawler1 = $client->request('GET', $getUrl);
    //Get total result from parser html.
    $count = $crawler1->filter('h2')->first();

    if($count->count() > 0){
        $totalres =$count->text();
    }

    //Each element we get result
    //print_r($count);
    if($totalres > 0){
        echo 'Total result is: <b>'.$totalres.'</b>';
        $items_list = array();
        $ceilVal =  ceil($totalres/100);
        $totalResult = array();
        for ($i = 0; $i < $ceilVal; $i++) {

            $crawler2 = $client->request('GET', $getUrl.'&p='.$i);

            $result = $crawler2->filter('.js-mark-record')->each(function ($node){

                $number = $node->filter('.qa-tm-number')->first();
                if($number->count() >0){
                    $number = $number->text();
                }

                //getting logo url, some url is null, that's why i used try catch for null exception
                try {
                    $logo_url = $node->filter('img')->attr('src');
                }catch (Exception $e){
                    $logo_url = "src is null";
                }

                $name = $node->filter('.trademark')->first();
                if($name->count() >0){
                    $name = $name->text();
                }

                $class = $node->filter('.classes')->first();
                if($class->count() >0){
                    $class = $class->text();
                }

                $status = $node->filter('.mark-line .status')->first();
                if($status->count() >0){
                    $status = $status->text();
                }

                $details_page_url = $node->filter('a')->attr('href');



                $items = array(
                    'number' =>  $number,
                    'logo_url' => $logo_url,
                    'name' => $name,
                    'classes' => $class,
                    'status' => $status,
                    'details_page_url' => "https://search.ipaustralia.gov.au"."".$details_page_url ?? null
                );

                return $items;
            });
            array_push($totalResult, $result);

        }
        echo "<pre>";
        print_r($totalResult);

    }else{
        echo "0 results";
        exit();
    }

}else{
    echo "Please fill out this field";
    exit();
}

?>
