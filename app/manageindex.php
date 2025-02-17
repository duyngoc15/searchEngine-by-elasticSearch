<?php
require "vendor/autoload.php";

$hosts = [
        'host' => 'elasticsearch',
        'port' => '9200',
        'scheme' => 'http',
];

$client = \Elasticsearch\ClientBuilder::create()
        ->setHosts($hosts)
        ->build();

$params = [
    'index' => 'account',

];
 $client->cat()->indices();



$act = $_GET['act'] ?? null;
$mgs = 'Chọn lệnh tạo hoặc xóa';
if ($act == 'create') {
    //Tạo Index: article
    $params = [
        'index' => 'account'
    ];

    $exist = $client->indices()->exists($params);
    if ($exist) {
        $mgs = "Index - account đã tồn tại - không cần tạo";
    }
    else {
        $params = [
            'index' => 'account',
            // 'body' => [
            //     'settings' => [
            //         'number_of_shards' => 1,
            //         'number_of_replicas' => 0,
            //         'analysis' => [
            //             'analyzer' => [ //Lọc loại bỏ thẻ html và index  chuyển đổi không dấu, chữ in thường
            //                 'my_analyzer' => [
            //                     'type' => 'custom',
            //                     'tokenizer' => 'icu_tokenizer',
            //                     "char_filter" => [ "html_strip"],
            //                     'filter' => ['icu_folding', 'lowercase', 'stop'] 
            //                 ],

            //             ]
            //         ], 

            //     ],
            // ],
        ];

       $response = $client->indices()->create($params);
       $filePath = '/var/www/html/accounts.json';
       $handle = fopen($filePath, 'r');
       if (!$handle) {
           die("Không thể mở file {$filePath}\n");
       }
       $bulkParams = [
        'index' => 'account', // Tên index muốn bulk vào (nếu muốn override action cặp dòng)
        'body'  => []
        ];
    
    // 4) Đọc file theo cặp dòng
    while (!feof($handle)) {
        // Dòng 1: action
        $actionLine = fgets($handle);
        if ($actionLine === false) {
            break;
        }
        $actionLine = trim($actionLine);
        if (empty($actionLine)) {
            continue; // Bỏ qua dòng trống
        }
    
        // Dòng 2: document
        $docLine = fgets($handle);
        if ($docLine === false) {
            break;
        }
        $docLine = trim($docLine);
        if (empty($docLine)) {
            continue;
        }
    
        // Parse JSON
        $actionJson = json_decode($actionLine, true);
        $docJson    = json_decode($docLine, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Nếu parse lỗi, bạn có thể log và continue, hoặc break.
            continue;
        }
    
        // 5) Đưa 2 "mảng" này vào body. 
        // $bulkParams['body'][] = $actionJson là "dòng action" (index, create, v.v.)
        // $bulkParams['body'][] = $docJson là nội dung document
        $bulkParams['body'][] = $actionJson;
        $bulkParams['body'][] = $docJson;
    }
    
        fclose($handle);
        if (!empty($bulkParams['body'])) {
            $response = $client->bulk($bulkParams);
            $mgs =  "Đã tạo index và import account document\n";
        } else {
            $mgs =  "Không có tài liệu nào để bulk.\n";
        }
        // $params = [
        //     'index' => 'account',
        //     'type' => 'account_type',
        //     'include_type_name' => true,
        //     'body' => [
        //         'account_type' => [
        //             'properties' => [
        //                 'title' => [
        //                     'type' => 'text',
        //                     'analyzer' => 'my_analyzer'
        //                 ],

        //             ]
        //         ]
        //     ]
        // ];
        
        // $response = $client->indices()->putMapping($params);

        // $mgs = "Index - account đã được thêm vào";
    }



}
else if ($act == 'delete') {
    // Xóa index:article
    $params = [
        'index' => 'account'
    ];

    $exist = $client->indices()->exists($params);
    if ($exist) {
        $rs = $client->indices()->delete($params);
        $mgs = "Đã xóa index - account";
    }
    else {
        $mgs = "Index - account không tồn tại";
    }

}


$exist = $client->indices()->exists(['index' => 'account']);

?>

<div class="card m-4">
    <div class="card-header display-4 text-danger">Quản lý Index</div>
    <div class="card-body">
        <? if (!$exist):?>
            <a href="http://localhost:8080/?page=manageindex&act=create" class="btn btn-primary">Tạo index <strong>account</strong></a>
        <? else:?>
            <a href="http://localhost:8080/?page=manageindex&act=delete" class="btn btn-danger">Xóa index <strong>account</strong></a>
        <? endif;?>

        <div class="alert alert-danger mt-4"><?=$mgs?></div>
    </div>
</div>