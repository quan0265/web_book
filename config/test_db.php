<?php 

include 'DB.php';

$data = NULL;

$config = [
	'db_host' => 'localhost',
	'db_port' => '3306',
	'db_user' => 'root',
	'db_pass' => '',
	'db_name' => 'db_test',
	'charset' => 'utf8mb4'
];
DB::connect($config);


// version full: have join, group by, having , order by, limit

// insert
	// DB::table('users')->insert(['name' => "\/'f'"]);
	// DB::table('users')->insert(['id' => '23', 'name' => 'j']);
	// last insert id if true reuturn number > 0 else reutrn 0;
	// echo DB::lastInsertId();

// update
// echo DB::table('users')->where('id', '1')->update(['name'=> 'e']);
// echo DB::table('users')->where('id', '2')->update(['name'=> '3']);

// delete
// echo DB::table('users')->where('id', 5)->delete();



// all result
	$data = DB::table('users')->whereRaw('id > ?', [1])->whereRaw('id < ?', [15])->get();
	// $data = DB::table('users')->where('id', '>', 1)->get();

// one result: default return result object
	// $data = DB::table('users')->first('array');

// find by id:
	// $data = DB::table('users')->find(3);

// count result: return a count of rows
	// $data = DB::table('users')->count();
	// $data = DB::table('users')->whereRaw('id > ?', [1])->count('id');
	// $data = DB::table('users')->count('DISTINCT name'); // returns a count of number rows with different 

// sum result: return sum of rows
	// $data = DB::table('users')->sum('id');



// query: select - join - where - group by - having - order by - limit
	// where - group by - having dùng chung trong where 

	// select
	// $data = DB::table('users')->select('name')->whereRaw('id > ?', [1])->get();
	// $data = DB::table('users')->select('DISTINCT id, name')->get();
	// $data = DB::select('id', 'name')->table('users')->get();

	// join
	// $data = DB::table('users')->select('users.id')->join('products', 'users.id', '=', 'products.id_user')->whereRaw('users.id > ?', [1])->get();
	// $data = DB::table('users')->select('users.id')->leftJoin('products', 'users.id', '=', 'products.id_user')->whereRaw('users.id > ?', [1])->get();
	// $data = DB::table('users')->select('users.id')->rightJoin('products', 'users.id', '=', 'products.id_user')->whereRaw('users.id > ?', [1])->get();

	// where - orWhere
	// $data = DB::table('users')->where('id', '>', 0)->get();
	// $data = DB::table('users')->where('name', 'like', '%name%')->get();
	// $data = DB::table('users')->where('id', 1)->orWhere('id', 2)->get();

	// $data = DB::table('users')->orWhere('id', 2)->get();
	$data = DB::table('users')->whereIn('id', [1, 2, 3])->get();
	$data = DB::table('users')->whereIn('id', [1, 2, 3])->orWhere('id', 2)->get();
		
	// whereRaw
	// $data = DB::table('users')->select('name')->whereRaw('id > ?', [1])->get();

	// group by - having - or having
	// $data = DB::table('users')->select('COUNT(id) as cout_id, name')->groupBy('name')->having('COUNT(id)', '>', 1)->get();
	// $data = DB::table('users')->select('COUNT(id) as cout_id, name')->groupBy('name')->having('COUNT(id)', '>', 0)->orHaving('COUNT(id)', '>=', 2)->get();

	// group by - havingRaw
	// $data = DB::table('users')->select('COUNT(id) as cout_id, name')->whereRaw('id > ?', [1])->groupBy('name')->havingRaw('COUNT(id) > ?', [1])->get();

	// order by - limit
	// $data = DB::table('users')->select('name')->whereRaw('id > ?', [1])->orderBy('name', 'asc')->limit(2)->get(); // limit 2
	// $data = DB::table('users')->select('name')->whereRaw('id > ?', [1])->orderBy('name', 'desc')->limit(5, 2)->get(); // offset 5, limit 2;



// all result by sql
	// $data = [1];
	// $data = [1, 2];
	// $data = DB::fetchAll("SELECT * FROM users WHERE id>=? AND id>?", $data, 'array');
	// $data = DB::fetchAll("SELECT * FROM users WHERE id>=? AND id>?", $data);

// one result by sql
	// $data = DB::fetchOne("SELECT * FROM users WHERE id>=? AND id>?", $data);

// count result by sql
	$data = DB::fetchCount("SELECT SUM(id) AS total FROM users WHERE id>=?", [1]);


// find by id
// $data = DB::table('users')->find(1);

// error
// echo DB::getSql() . '<br>';
// DB::error();
// echo $data;
// echo '<pre>';
if (isset($data)) {
	var_dump($data);
}
DB::debug();

