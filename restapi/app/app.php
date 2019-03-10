<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */
use Phalcon\Http\Response;
use Phalcon\Config;

/**
 * Add your routes here
 */
$app->get('/', function () {
    echo $this['view']->render('index');
});

$app->get('/zoo/comment/{oid:[0-9]+}/{page:[0-9]+}', function ($id, $page) {
    $data = [];
    $limit = 10;
    $page = $page > 1 ? (int)$page : 1;
    $offset = ($page - 1) * $limit;
    $comment = Comment::find(
        [
            'conditions'    => 'oid = ?1 and is_delete = 0',
            'bind'          => [
                1   => $id
            ],
            'limit'         => $limit,
            'offset'        => $offset,
            'order'         => 'ctime DESC'
        ]
    );
    $count = Comment::count(
        [
            'conditions'    => 'oid = ?1 and is_delete = 0',
            'bind'          => [
                1   => $id
            ]
        ]
    );

    $data = [
        'total'     => $count,
        'page'      => $page,
        'pagesize'  => $limit,
        'list'      => $comment
    ];

    return response($data);
});

$app->post('/zoo/comment/{id:[0-9]+}', function ($id) use ($app) {
    $data = $app->request->getJsonRawBody();
    
    Comment::saveContent($id, $data->content);

    return response('');
});

$app->put('/zoo/comment', function () {

});

$app->delete('/zoo/comment', function () {

});

// zoo island
$app->get('/zoo/island', function () {
    $data = [];
    $islands = Island::find(['columns' => 'id, name, class_name']);
    foreach ($islands as $key => $island) {
        $item = [
            'id' => $island->id,
            'name'  => $island->name,
            'class_name'    => $island->class_name
        ];
        $races = Race::find(
            [
                'conditions'    => 'island_id = ?1',
                'columns'       => 'id, name, content',
                'bind'          => [
                    1   => $island->id
                ]
            ]
        );

        $item['races'] = $races;
        $data[] = $item;
    }

    return response($data);
});

// zoo race
// $app->get('/zoo/race/{id:[0-9]+}', function ($id) {
//     $race = Race::find(
//         [
//             'conditions'    => 'island_id = ?1',
//             'columns'       => 'id, name, content',
//             'bind'          => [
//                 1   => $id
//             ]
//         ]
//     );

//     return response($race);
// });

// zoo animal
$app->get('/zoo/race/{id:[0-9]+}', function ($id) {
    $data = ['info' => [], 'list' => []];

    $race = Race::findFirst($id);
    $data['info'] = $race;

    $animals = Animal::find(
        [
            'conditions'    => 'race_id = ?1',
            'columns'       => 'id, name, [desc], image, skill',
            'bind'          => [
                1   => $id
            ]
        ]
    );

    $config = include APP_PATH . "/config/zoo.php";
    foreach ($animals as $animal) {
        $item = [
            'id'        => $animal->id,
            'name'      => $animal->name,
            'desc'      => $animal->desc,
            'image'     => $animal->image,
            'skills'    => []
        ];

        $skills = $animal->skill ? json_decode($animal->skill) : [];
        foreach ($skills as $k => $v) {
            $item['skills'][] = [
                'id'    => $k,
                'text'  => str_replace('x', $v, $config[$k]),
                'value' => $v
            ];
        }

        $data['list'][] = $item;
    }

    return response($data);
});

$app->get('/zoo/top', function () {
    $max = [];
    $config = include APP_PATH . "/config/zoo.php";
    $animals = Animal::find(['id_delete' => 0]);
    
    foreach ($animals as $animal) {
        $skills = json_decode($animal->skill, 1);
        foreach ($skills as $key => $value) {
            if (!isset($max[$key])) {
                $max[$key] = [
                    'value'     => 0,
                    'text'      => str_replace('x', 0, $config[$key]),
                    'animals'   => []
                ];
            }

            if ($max[$key]['value'] < $value) {
                $max[$key]['value'] = $value;
                $max[$key]['text'] = str_replace('x', $value, $config[$key]);
                $max[$key]['animals'] = [];
                $max[$key]['animals'][$animal->id] = $animal;
            } else if ($max[$key]['value'] == $value) {
                $max[$key]['animals'][$animal->id] = $animal;
            }
        }
    }

    return response($max);
});

$app->get('/zoo/skill/{id:[0-9]+}', function ($id) {
    $data = [];
    $list = [];
    $config = include APP_PATH . "/config/zoo.php";
    $animals = Animal::find(['id_delete' => 0]);
    
    foreach ($animals as $animal) {
        $skills = json_decode($animal->skill, 1);
        foreach ($skills as $key => $value) {
            if ($key != $id) {
                continue;
            }

            if (!isset($list[$value])) {
                $list[$value] = [];
            }

            $list[$value][$animal->id] = $animal;
        }
    }

    krsort($list);
    $data = [
        'info'  => $config[$id],
        'list'  => $list
    ];

    return response($data);
});

$app->get('/zoo/detail/{id:[0-9]+}', function ($id) {
    $data = [];
    $config = include APP_PATH . "/config/zoo.php";
    $animal = Animal::findFirst($id);
    $data['info'] = $animal;

    $skills = json_decode($animal->skill, 1);
    $data['skills'] = [];
    foreach ($skills as $k => $v) {
        $data['skills'][] = [
            'id'    => $k,
            'text'  => str_replace('x', $v, $config[$k]),
            'value' => $v
        ];
    }

    return response($data);
});

/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});

function response ($res) {
    $response = new Response();

    if ($res === false) {
        $response->setJsonContent(
            [
                'code'  => '400',
                'msg'   => '',
                'data'  => ''
            ]
        );
    } else {
        $response->setJsonContent(
            [
                'code'  => '100',
                'msg'   => '',
                'data'  => $res
            ]
        );
    }

    return $response;
}