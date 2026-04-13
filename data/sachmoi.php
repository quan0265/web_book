<?php


function curl_get($url) {
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => '', // tự decode gzip
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch);
    }

    curl_close($ch);

    return $data;
}

function parse_sitemap($url) {
    $xmlString = curl_get($url);
    if (!$xmlString) return;

    $xml = simplexml_load_string($xmlString);

    // Nếu là sitemap index
    if (isset($xml->sitemap)) {
        foreach ($xml->sitemap as $sitemap) {
            parse_sitemap((string)$sitemap->loc);
        }
    }

    // Nếu là urlset
    $data = [];
    if (isset($xml->url)) {
        foreach ($xml->url as $urlNode) {
            $loc = (string)$urlNode->loc;

            $data_item['url'] = $loc;

            // Lấy image (nếu có)
            $namespaces = $urlNode->getNamespaces(true);
            if (isset($namespaces['image'])) {
                $images = $urlNode->children($namespaces['image']);

                foreach ($images->image as $img) {
                    $data_item['image'] = (string)$img->loc;
                }
            }

            $data[] = $data_item;
        }
    }
    return $data;
}

// chạy
// $url = "https://sachmoi.net/download-sitemap.xml";
$urls = [
    "https://sachmoi.net/download-sitemap.xml",
    "https://sachmoi.net/download-sitemap2.xml",
    "https://sachmoi.net/download-sitemap3.xml",
    "https://sachmoi.net/download-sitemap4.xml",
    "https://sachmoi.net/download-sitemap5.xml",
    "https://sachmoi.net/download-sitemap6.xml",
    "https://sachmoi.net/download-sitemap7.xml",
    "https://sachmoi.net/download-sitemap8.xml",
];

foreach ($urls as $url) {
    $data_old = file_get_contents('sachmoi.json');
    $data_old = json_decode($data_old, true);
    $data_old = is_array($data_old) ? $data_old : [];

    $data = parse_sitemap($url);
    $data = array_merge($data_old, $data);
    file_put_contents('sachmoi.json', json_encode($data, JSON_PRETTY_PRINT));
}


?>