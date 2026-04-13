<?php

$data = file_get_contents('./data/sachmoi.json');
$data = json_decode($data, true);

// foreach ($data as $item) {
//     $url = $item['url'];
//     $image = $item['image'];
// }

function is_show($text) {
    $arrs = [
        'ban-hang',
        'khach-hang',
        'vat-ly',
        'toan-hoc',
        'mon-toan',
        'hoa-hoc',
        'de-thi',
        'lich-su',
        'trac-nghiem',
        'tieng-anh',
        'mi-thuat',
        'tin-hoc',
        'dia-li',
        'ngu-van',
        'sinh-hoc',
        'giai-de',
        'lop-11',
    ];

    // Escape các ký tự đặc biệt để tránh lỗi regex
    $pattern = '/(' . implode('|', array_map('preg_quote', $arrs)) . ')/i';

    return !preg_match($pattern, $text);
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách URL & Image</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        a {
            color: inherit;
            text-decoration: none;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Danh sách URL & Image</h1>

    <table class="table table-striped table-bordered align-middle">
        <thead>
        <tr>
            <th style="width: 50px">#</th>
            <th style="width: 100px">Image</th>
            <th>URL</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($data)): ?>
            <?php $i = 1; ?>
            <?php foreach ($data as $row): ?>
                <?php
                $url = isset($row['url']) ? $row['url'] : '';
                $image  = isset($row['image']) ? $row['image'] : '';

                if (!is_show($row['url'])) {
                    continue;
                }

                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td style="max-width: 200px; width: 200px;">
                        <?php if ($image): ?>
                            <!-- <img src="<?php echo htmlspecialchars($image); ?>"
                                alt=""
                                class="img-fluid img-thumbnail"
                                style="max-height: 150px; max-width: 50px; width: 50px;"> -->
                        <?php else: ?>
                            <span class="text-muted">Không có image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($url): ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank">
                                <?php $url = str_replace("https://sachmoi.net/download/", "", htmlspecialchars($url)); ?>
                                <?php echo str_replace("-", " ", htmlspecialchars($url)); ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center text-muted">
                    Không có dữ liệu
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>