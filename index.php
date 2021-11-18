<?
require __DIR__ . "/vendor/autoload.php";
require_once("vendor/fabpot/goutte/Goutte/Client.php");

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
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

if (isset($_POST["txtInput"]) && $_POST["txtInput"] != "") {
    $term = $_POST["wv"][0];
    echo $term;
    ini_set('max_execution_time', '500');
    $client = new Client(HttpClient::create(['timeout' => 500]));
    $crawler1 = $client->request('GET', 'https://search.ipaustralia.gov.au/trademarks/search/quick/result?q=' . $term);
    $count = $crawler1->filter('h2')->each(function ($node) {
        "<br>Total result is: ".$node->text() ?? null;
        return ceil(($node->text() ?? null) /100);
    });

    for ($i = 0; $i < $count[0]; $i++) {
        $crawler2 = $client->request('GET', 'https://search.ipaustralia.gov.au/trademarks/search/quick/result?q='.$term.'&p='.$i);

        $result = $crawler2->filter('.js-mark-record')->each(function ($node) {

            $number = $node->filter('.qa-tm-number')->each(function ($a) {
                return  $a->text();
            });

            $name = $node->filter('.trademark')->each(function ($a) {
                return  $a->text();
            });

            $class = $node->filter('.classes')->each(function ($a) {
                return  $a->text();
            });

            $status = $node->filter('.status')->each(function ($a) {
                return  $a->text();
            });

            $img = $node->filter('img')->each(function ($a) {
                return  $a->attr('src');
            });

            $details_page_url =  $node->filter('a')->each(function ($a) {
                return  $a->attr('href');
            });


            $items = [
                'number' =>  $number[0],
                'logo_url' => $img[0] ?? null,
                'name' => $name[0],
                'classes' => $class[0],
                'status' => $status[0],
                'details_page_url' => "https://search.ipaustralia.gov.au"."".$details_page_url[0] ?? null
            ];

            return $items;
        });
    }
    echo '<pre>';
    print_r($result ?? null);

}else{
    echo "Please fill out this field";
    exit();
}

?>