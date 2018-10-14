<?php
if (isset($_POST['parse'])) {
    if (empty($_POST['feed'])) {
        throw new Exception('Введите адрес фида');
    }

    $feed = $_POST['feed'];
    $feedXml = simplexml_load_file($feed);

    if (!$feedXml) {
        throw new Exception('Ошибка загрузки фида');
    }

    $buildings = [];

    foreach ($feedXml as $tag => $offer) {
        if ($tag != 'offer') {
            continue;
        }

        $buildingName = (string)$offer->{'building-name'};

        if (empty($buildingName)) {
            continue;
        }

        $price = (int)$offer->price->value;
        $image = (string)$offer->image[0];
        $houseId = (int)$offer->{'yandex-house-id'};
        $lastUpdate = (string)$offer->{'last-update-date'};
        $category = (string)$offer->category;

        if (isset($buildings[$buildingName]['min_price'])) {
            if ($price < $buildings[$buildingName]['min_price']) {
                $buildings[$buildingName]['min_price'] = $price;
            }
        } else {
            $buildings[$buildingName]['min_price'] = $price;
        }

        if (isset($buildings[$buildingName]['max_price'])) {
            if ($price > $buildings[$buildingName]['max_price']) {
                $buildings[$buildingName]['max_price'] = $price;
            }
        } else {
            $buildings[$buildingName]['max_price'] = $price;
        }

        if (!isset($buildings[$buildingName]['image'])) {
            $buildings[$buildingName]['image'] = $image;
        }

        if ($houseId != 0) {
            $buildings[$buildingName]['houses'][] = $houseId;
        }

        if ($category == 'квартира' || $category == 'flat') {
            if (isset($buildings[$buildingName]['flats'])) {
                $buildings[$buildingName]['flats'] += 1;
            } else {
                $buildings[$buildingName]['flats'] = 1;
            }
        }

        if (isset($buildings[$buildingName]['last_update'])) {
            if (strtotime($lastUpdate) > strtotime($buildings[$buildingName]['last_update'])) {
                $buildings[$buildingName]['last_update'] = $lastUpdate;
            }
        } else {
            $buildings[$buildingName]['last_update'] = $lastUpdate;
        }
    }

    $dbconn = pg_connect("host=localhost dbname=parser user=postgres password=1!qQ")
    or die('Не удалось соединиться: ' . pg_last_error());
    $affected = 0;

    foreach ($buildings as $buildingName => $info) {
        if (!isset($info['houses'])) {
            $info['houses'] = [0]; // должен быть минимум один корпус
        }

        $uniqueHouses = array_unique($info['houses']);
        $countHouses = count($uniqueHouses);
        $buildings[$buildingName]['houses'] = $countHouses;
        $query = "INSERT INTO offer (building_name, min_price, max_price, image, houses, flats, last_update) 
            VALUES ('$buildingName', '{$info['min_price']}', '{$info['max_price']}', '{$info['image']}', 
            '{$countHouses}', '{$info['flats']}', '{$info['last_update']}')";
        $result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
        $affected += pg_affected_rows($result);
        pg_free_result($result);
    }

    pg_close($dbconn);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Парсер</title>
</head>
<body>
<div class="container">
    <h1>Парсер</h1>
    <form method="post" class="mb-2">
        <div class="form-group">
            <label for="exampleInputFeed">Фид</label>
            <input name="feed" type="text" class="form-control" id="exampleInputFeed" placeholder="Введите фид"
                   required>
        </div>
        <button type="submit" name="parse" class="btn btn-primary">Парсить</button>
    </form>
    <?php if ($affected != 0): ?>
        <div class="alert alert-primary" role="alert">
            Добавлено <?= $affected ?> ЖК
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if (!empty($buildings)): ?>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">ЖК</th>
                <th scope="col">Минимальная цена</th>
                <th scope="col">Максимальная цена</th>
                <th scope="col">Изображение</th>
                <th scope="col">Корпусов</th>
                <th scope="col">Квартир</th>
                <th scope="col">Обновление</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($buildings as $buildingName => $info): ?>
                <tr>
                    <td><?= $buildingName ?></td>
                    <td><?= $info['min_price'] ?></td>
                    <td><?= $info['max_price'] ?></td>
                    <td>
                        <img class="img-fluid" src="<?= $info['image'] ?>">
                    </td>
                    <td><?= $info['houses'] ?></td>
                    <td><?= $info['flats'] ?></td>
                    <td><?= $info['last_update'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>