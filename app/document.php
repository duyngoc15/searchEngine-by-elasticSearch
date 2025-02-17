
<?php
use Elasticsearch\Client;
require 'vendor/autoload.php';

//Cấu hình kết nối đến ES
$hosts = [
    [
        'host' => 'elasticsearch',
        'port' => '9200',
        'scheme' => 'http',             //https
    ],

];

//Tạo đối tượng Client
$client = \Elasticsearch\ClientBuilder::create()
    ->setHosts($hosts)
    ->build();




if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['keywords']) && isset($_POST['id'])) {

    $params = [
        'index' => 'article',
        'type'  => 'article_type',
        'id'    => $_POST['id'],

        'body'  => [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'keywords' => explode(',', $_POST['keywords'])
        ]
    ];


    $response = $client->index($params);


    echo 'Đã tạo, cập nhật ID ' . $_POST['id'];
}




?>

<div class="card m-4">
    <div class="card-header display-4 text-danger">Tạo / cập nhật Document</div>
    <div class="card-body">

        <form method="post" class="form">

            <div class="form-group">
                <label>Account Number</label>
                <input name="account_number" class="form-control">
            </div>

            <div class="form-group">
                <label>Balance</label>
                <input name="balance" class="form-control">
            </div>

            <div class="form-group">
                <label>First Name</label> <br>
                <input name="firstname" class="form-control"></input>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input name="lastname" class="form-control">
            </div>

            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" class="form-control">
            </div>

            <div class="form-group">
                <label>Gender</label>
                <div>
                    <label class="radio-inline">
                        <input type="radio" name="gender" value="male" class="form-control"> Male
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="gender" value="female" class="form-control"> Female
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input name="address" class="form-control">
            </div>

            <div class="form-group">
                <label>Employer</label>
                <input name="employer" class="form-control">
            </div>
        
            <div class="form-group">
                <label>Email</label>
                <input name="email" class="form-control">
            </div>

            <div class="form-group">
                <label>City</label>
                <input name="city" class="form-control">
            </div>

            <div class="form-group">
                <label>State</label>
                <input name="state" class="form-control">
            </div>


            <input type="submit" value="Update" class="btn btn-danger">
        </form>

    </div>
</div>
