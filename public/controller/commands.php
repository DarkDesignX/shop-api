<?php
	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Slim\Factory\AppFactory;
	use ReallySimpleJWT\Token;

	/**
     * @OA\Post(
     *     path="/Authenticate",
     *     summary="Used to authenticate and obtain an access token that will be stored in the cookies.",
     *     tags={"General"},
     *     requestBody=@OA\RequestBody(
     *         request="/Authenticate",
     *         required=true,
     *         description="The credentials are passed to the server via the request body.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", example="admin"),
     *                 @OA\Property(property="password", type="string", example="sec!ReT423*&")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully authenticated")),
     *     @OA\Response(response="401", description="Invalid credentials")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */
	$app->post("/Authenticate", function (Request $request, Response $response, $args) {
		global $api_username;
		global $api_password;

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

		$username = $request_data["username"];
		$password = $request_data["password"];

		if ($username != $api_username || $password != $api_password) {
			error("Invalid credentials.", 401);
		}

		//Generate the access token and store it in the cookies.
		$token = Token::create($username, $password, time() + 3600, "localhost");

		setcookie("token", $token);

		echo "You are now authentified";

		return $response;
	});

	/**
     * @OA\Post(
     *     path="/Category",
     *     summary="Used to create a new category.",
     *     tags={"General"},
     *     requestBody=@OA\RequestBody(
     *         request="/Category",
     *         required=true,
     *         description="The data is being saved in the database.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="name", type="string", example="movies")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully created a new category")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */

	$app->post("/Category", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

		if (!isset($request_data["active"]) || !is_numeric($request_data["active"])) {
			error("Please provide an integer number for the \"active\" field.", 400);
		}
		if (!isset($request_data["name"])) {
			error("Please provide a \"name\" field.", 400);
		}

		$active = intval($request_data["active"]);
		$name = strip_tags(addslashes($request_data["name"]));

		if (empty($name)) {
			error("The \"name\" field must not be empty.", 400);
		}
		if (empty($active)) {
			error("The \"active\" field must not be empty.", 400);
		}

		if (strlen($name) > 500) {
			error("The name is too long. Please enter less than or equal to 500 characters.", 400);
		}
		if (is_float($active)) {
			error("The active-data must not have decimals.", 400);
		}

		if (create_new_category($active, $name) === true) {
			http_response_code(201);
			echo "A new category has been created";
		}
		else {
			error("An error occurred while saving the student data.", 500);
		}

		return $response;
	});

	/**
     * @OA\Post(
     *     path="/Product",
     *     summary="Used to create a new product.",
     *     tags={"General"},
     *     requestBody=@OA\RequestBody(
     *         request="/Product",
     *         required=true,
     *         description="The data is being saved in the database.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="sku", type="string", example="?"),
	 *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="id_category", type="integer", example="1"),
	 *                 @OA\Property(property="name", type="string", example="Iron Man (2008)"),
	 *                 @OA\Property(property="image", type="string", example=""),
	 *                 @OA\Property(property="description", type="text", example="Tony Stark, rich heir to the Stark Industries technology company, is captured in Afghanistan. He manages to escape with a suit of armor he built himself. Back in America, he wants to stop his company's arms sales. His father's longtime business partner Obadia Stane is against Tony's proposals. As Tony gets more involved in the day-to-day business, he discovers dark secrets of his company."),
	 *                 @OA\Property(property="price", type="decimal", example="19.50"),
	 *                 @OA\Property(property="stock", type="integer", example="200")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully created a new product")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */

	$app->post("/Product", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

		if (!isset($request_data["sku"])) {
			error("Please provide a \"sku\" field.", 400);
		}
		if (!isset($request_data["active"]) || !is_numeric($request_data["active"])) {
			error("Please provide an integer number for the \"active\" field.", 400);
		}
		if (!isset($request_data["id_category"]) || !is_numeric($request_data["id_category"])) {
			error("Please provide an integer number for the \"id_category\" field.", 400);
		}
		if (!isset($request_data["name"])) {
			error("Please provide a \"name\" field.", 400);
		}
		if (!isset($request_data["image"])) {
			error("Please provide an \"image\" field.", 400);
		}
		if (!isset($request_data["description"])) {
			error("Please provide a \"description\" field.", 400);
		}
		if (!isset($request_data["price"]) || !is_numeric($request_data["price"])) {
			error("Please provide an integer number for the \"price\" field.", 400);
		}
		if (!isset($request_data["stock"]) || !is_numeric($request_data["stock"])) {
			error("Please provide an integer number for the \"stock\" field.", 400);
		}

		$sku = strip_tags(addslashes($request_data["sku"]));
		$active = intval($request_data["active"]);
		$id_category = intval($request_data["id_category"]);
		$name = strip_tags(addslashes($request_data["name"]));
		$image = strip_tags(addslashes($request_data["image"]));
		$description = strip_tags(addslashes($request_data["description"]));
		$price = intval($request_data["price"]);
		$stock = intval($request_data["stock"]);

		if (empty($sku)) {
			error("The \"sku\" field must not be empty.", 400);
		}
		if (empty($active)) {
			error("The \"active\" field must not be empty.", 400);
		}
		if (empty($id_category)) {
			error("The \"id_category\" field must not be empty.", 400);
		}
		if (empty($name)) {
			error("The \"name\" field must not be empty.", 400);
		}
		if (empty($image)) {
			error("The \"image\" field must not be empty.", 400);
		}
		if (empty($description)) {
			error("The \"description\" field must not be empty.", 400);
		}
		if (empty($price)) {
			error("The \"price\" field must not be empty.", 400);
		}
		if (empty($stock)) {
			error("The \"stock\" field must not be empty.", 400);
		}

		if (strlen($sku) > 100) {
			error("The sku is too long. Please enter less than or equal to 100 characters.", 400);
		}
		if (is_float($active)) {
			error("The active-data must not have decimals.", 400);
		}
		if (is_float($id_category)) {
			error("The id category must not have decimals.", 400);
		}
		if (strlen($name) > 500) {
			error("The name is too long. Please enter less than or equal to 500 characters.", 400);
		}
		if (strlen($image) > 1000) {
			error("The image data is too long. Please enter less than or equal to 1000 characters.", 400);
		}
		if ($price < 0 || $price > 65.2) {
			error("The price must be between 0 and 65,2 dollars.", 400);
		}
		if (is_float($stock)) {
			error("The stock must not have decimals.", 400);
		}

		if (create_new_product($sku, $active, $name, $image, $description, $price, $stock) === true) {
			http_response_code(201);
			echo "A new product has been created";
		}
		else {
			error("An error occurred while saving the product data.", 500);
		}

		return $response;
	});

	/**
     * @OA\Get(
     *     path="/Category/{category_id}",
     *     summary="Used to update a category in the database.",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="Used to find the data from the database with the right ID.",
     *         @OA\Schema(
     *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="name", type="string", example="movies")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully found the category")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
	 */

	$app->get("/Category/{category_id}", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$category_id = intval($args["category_id"]);

		$category = get_category($category_id);

		if (!$category) {
			error("No category found for the ID " . $category_id . ".", 404);
		}
		else if (is_string($category)) {
			error($category, 500);
		}
		else {
			echo json_encode($category);
		}

		return $response;
	});

	/**
     * @OA\Get(
     *     path="/Product/{product_id}",
     *     summary="Used to update a category in the database.",
     *     tags={"Product)"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="Used to find the data from the database with the right ID.",
     *         @OA\Schema(
     *             @OA\Property(property="sku", type="string", example="?"),
	 *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="id_category", type="integer", example="1"),
	 *                 @OA\Property(property="name", type="string", example="Iron Man (2008)"),
	 *                 @OA\Property(property="image", type="string", example=""),
	 *                 @OA\Property(property="description", type="text", example="Tony Stark, rich heir to the Stark Industries technology company, is captured in Afghanistan. He manages to escape with a suit of armor he built himself. Back in America, he wants to stop his company's arms sales. His father's longtime business partner Obadia Stane is against Tony's proposals. As Tony gets more involved in the day-to-day business, he discovers dark secrets of his company."),
	 *                 @OA\Property(property="price", type="decimal", example="19.50"),
	 *                 @OA\Property(property="stock", type="integer", example="200")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully found the product")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
	 */

	$app->get("/Product/{product_id}", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$product_id = intval($args["product_id"]);

		$product = get_product($product_id);

		if (!$product) {
			error("No category found for the ID " . $product_id . ".", 404);
		}
		else if (is_string($product)) {
			error($product, 500);
		}
		else {
			echo json_encode($product);
		}

		return $response;
	});

	/**
     * @OA\Put(
     *     path="/Category/{category_id}",
     *     summary="Used to update a category in the database.",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="category ID can be found in the database and updated with new data",
     *         @OA\Schema(
     *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="name", type="string", example="movies")
     *         )
     *     ),
     *     requestBody=@OA\RequestBody(
     *         request="/Category",
     *         required=true,
     *         description="Used to fetch the data from the database with the right id.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="active", type="tinyint", example="0"),
     *                 @OA\Property(property="name", type="string", example="games")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully updated the category")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */

	$app->put("/Category/{category_id}", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$category_id = intval($args["category_id"]);

		$category = get_category($category_id);

		if (!$category) {
			error("No category found for the ID " . $category_id . ".", 404);
		}
		else if (is_string($category)) {
			error($category, 500);
		}

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

		if (!isset($request_data["active"]) || !is_numeric($request_data["active"])) {
			error("Please provide an integer number for the \"active\" field.", 400);
		}
		if (!isset($request_data["name"])) {
			$name = strip_tags(addslashes($request_data["name"]));

			if (empty($name)) {
				error("The \"name\" field must not be empty.", 400);
			}
	
			if (strlen($name) > 500) {
				error("The name is too long. Please enter less than or equal to 500 characters.", 400);
			}

			$category["name"] = $name;
		}

		if (isset($request_data["active"])) {
			if (!is_numeric($request_data["active"])) {
				error("Please provide an integer number for the \"active\" field.", 400);
			}

			$active = intval($request_data["active"]);

			if (is_float($active)) {
				error("The active field must not have decimals.", 400);
			}

			$category["active"] = $active;
		}

		if (update_category($category_id, $category["active"], $category["name"])) {
			echo "The category has been successfully updated";
		}
		else {
			error("An error occurred while saving the category data.", 500);
		}

		return $response;
	});

	/** 
     * @OA\Put(
     *     path="/Product/{product_id}",
     *     summary="Used to update a product in the database.",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="product ID can be found in the database and updated with new data",
     *         @OA\Schema(
     *                 @OA\Property(property="sku", type="string", example="?"),
	 *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="id_category", type="integer", example="1"),
	 *                 @OA\Property(property="name", type="string", example="Iron Man (2008)"),
	 *                 @OA\Property(property="image", type="string", example=""),
	 *                 @OA\Property(property="description", type="text", example="Tony Stark, rich heir to the Stark Industries technology company, is captured in Afghanistan. He manages to escape with a suit of armor he built himself. Back in America, he wants to stop his company's arms sales. His father's longtime business partner Obadia Stane is against Tony's proposals. As Tony gets more involved in the day-to-day business, he discovers dark secrets of his company."),
	 *                 @OA\Property(property="price", type="decimal", example="19.50"),
	 *                 @OA\Property(property="stock", type="integer", example="200")
     *         )
     *     ),
     *     requestBody=@OA\RequestBody(
     *         request="/Product",
     *         required=true,
     *         description="Used to fetch the product data from the database with the right id.",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="sku", type="string", example="?"),
	 *                 @OA\Property(property="active", type="tinyint", example="0"),
     *                 @OA\Property(property="id_category", type="integer", example="2"),
	 *                 @OA\Property(property="name", type="string", example="8 Mile "),
	 *                 @OA\Property(property="image", type="string", example=""),
	 *                 @OA\Property(property="description", type="text", example="Jimmy lives with his alcoholic mother and little sister in a run-down trailer. During the day he works on an assembly line in a car factory. While he has to deal with everyday problems, he dreams, together with his friends Future, DJ Iz and Sol, of a professional career as a rapper."),
	 *                 @OA\Property(property="price", type="decimal", example="14.50"),
	 *                 @OA\Property(property="stock", type="integer", example="199")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully updated the product")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */


	/** 
	 * 
	 * $app->put("/Product/{product_id}", function (Request $request, Response $response, $args) {
	 *	require "controller/authentication.php";
	 *
	 *	$product_id = intval($args["product_id"]);
 	 *
	 *	$product = get_product($product_id);
	 *
	 * });
	 *
	 */
	

	/**
     * @OA\Delete(
     *     path="/Category/{category_id}",
     *     summary="Used to delete a registration.",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="Used to delete data from database with the right id.",
     *         @OA\Schema(
     *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="name", type="string", example="movies")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully deleted the category")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */

	$app->delete("/Category/{category_id}", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$category_id = intval($args["category_id"]);

		$result = delete_category($category_id);

		if (!$result) {
			error("No category found for the ID " . $category_id . ".", 404);
		}
		else if (is_string($result)) {
			error($category, 500);
		}
		else {
			echo json_encode($result);
		}

		return $response;
	});

	/**
     * @OA\Delete(
     *     path="/Product/{product_id}",
     *     summary="Used to delete a product.",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="Used to delete data from database with the right id.",
     *         @OA\Schema(
     *                 @OA\Property(property="sku", type="string", example="?"),
	 *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="id_category", type="integer", example="1"),
	 *                 @OA\Property(property="name", type="string", example="Iron Man (2008)"),
	 *                 @OA\Property(property="image", type="string", example=""),
	 *                 @OA\Property(property="description", type="text", example="Tony Stark, rich heir to the Stark Industries technology company, is captured in Afghanistan. He manages to escape with a suit of armor he built himself. Back in America, he wants to stop his company's arms sales. His father's longtime business partner Obadia Stane is against Tony's proposals. As Tony gets more involved in the day-to-day business, he discovers dark secrets of his company."),
	 *                 @OA\Property(property="price", type="decimal", example="19.50"),
	 *                 @OA\Property(property="stock", type="integer", example="200")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successfully deleted the product")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))
     * )
	 */

	$app->delete("/Product/{product_id}", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$product_id = intval($args["product_id"]);

		$result = delete_product($product_id);

		if (!$result) {
			error("No product found for the ID " . $product_id . ".", 404);
		}
		else if (is_string($result)) {
			error($product, 500);
		}
		else {
			echo json_encode($result);
		}

		return $response;
	});

	/**
     * @OA\Get(
     *     path="/Categories",
     *     summary="Used to get all the categories",
     *     tags={"General"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="Isn't necessarily used.",
     *         @OA\Schema(
     *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="name", type="string", example="movies")
     *         )
     *     ),
	 *     @OA\Response(response="200", description="Successfully created a new registration")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))	
	 * ) 
	 */

	$app->get("/Categories", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$categories = get_all_categories();

		if (is_string($categories)) {
			error($categories, 500);
		}
		else {
			echo json_encode($categories);
		}

		return $response;
	});

	/**
     * @OA\Get(
     *     path="/Products",
     *     summary="Used to get all the products",
     *     tags={"General"},
     *     @OA\Parameter(
     *         name="parameter",
     *         in="path",
     *         required=true,
     *         description="Isn't necessarily used.",
     *         @OA\Schema(
     *                 @OA\Property(property="sku", type="string", example="?"),
	 *                 @OA\Property(property="active", type="tinyint", example="1"),
     *                 @OA\Property(property="id_category", type="integer", example="1"),
	 *                 @OA\Property(property="name", type="string", example="Iron Man (2008)"),
	 *                 @OA\Property(property="image", type="string", example=""),
	 *                 @OA\Property(property="description", type="text", example="Tony Stark, rich heir to the Stark Industries technology company, is captured in Afghanistan. He manages to escape with a suit of armor he built himself. Back in America, he wants to stop his company's arms sales. His father's longtime business partner Obadia Stane is against Tony's proposals. As Tony gets more involved in the day-to-day business, he discovers dark secrets of his company."),
	 *                 @OA\Property(property="price", type="decimal", example="19.50"),
	 *                 @OA\Property(property="stock", type="integer", example="200")
     *         )
     *     ),
	 *     @OA\Response(response="200", description="Successfully created a new registration")),
     *     @OA\Response(response="400", description="Invalid data requested")),
     *     @OA\Response(response="500", description="Internal server error"))	
	 * ) 
	 */

	$app->get("/Products", function (Request $request, Response $response, $args) {
		require "controller/authentication.php";

		$products = get_all_products();

		if (is_string($products)) {
			error($products, 500);
		}
		else {
			echo json_encode($products);
		}

		return $response;
	});
?>