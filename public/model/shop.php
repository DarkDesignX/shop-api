<?php
	require "model/database_product.php";
	require "model/database_category.php";

	function get_all_categories() {
		global $database_category;

		$result = $database_category->query("SELECT * FROM category");

		if (!$result) {
			return "An error occurred while fetching the categories.";
		}
		else if ($result === true || $result->num_rows == 0) {
			return array();
		}
		
		$categories = array();

		while ($category = $result->fetch_assoc()) {
			$categories[] = $category;
		}

		return $categories;
	}

	function get_all_products() {
		global $database_product;

		$result = $database_product->query("SELECT * FROM product");

		if (!$result) {
			return "An error occurred while fetching the registrations.";
		}
		else if ($result === true || $result->num_rows == 0) {
			return array();
		}
		
		$products = array();

		while ($product = $result->fetch_assoc()) {
			$products[] = $product;
		}

		return $products;
	}

	function create_new_category($active, $name) {
		global $database_category;

		$result = $database_category->query("INSERT INTO category(active, name) VALUES($active,'$name')");

		if (!$result) {
			return false;
		}
		
		return true;
	}

	function create_new_product($sku, $active, $id_category, $name, $image, $description, $price, $stock) {
		global $database_product;

		$result = $database_product->query("INSERT INTO product(sku, active, id_category, name, image, description, price, stock) VALUES('$sku', $active, $id_category, '$name', '$image', '$description', $price, $stock)");

		if (!$result) {
			return false;
		}
		
		return true;
	}

	function get_category($category_id) {
		global $database_category;

		$result = $database_category->query("SELECT * FROM category WHERE category_id = $category_id");

		if (!$result) {
			return "An error occurred while fetching the category.";
		}
		else if ($result === true || $result->num_rows == 0) {
			return null;
		}
		else {
			$category = $result->fetch_assoc();

			return $category;
		}
	}

	function get_product($product_id) {
		global $database_product;

		$result = $database_product->query("SELECT * FROM product WHERE product_id = $product_id");

		if (!$result) {
			return "An error occurred while fetching the product.";
		}
		else if ($result === true || $result->num_rows == 0) {
			return null;
		}
		else {
			$product = $result->fetch_assoc();

			return $product;
		}
	}

	function update_category($category_id, $active, $name) {
		global $database_category;

		$result = $database_category->query("UPDATE category SET active = $active, name = '$name' WHERE category_id = $category_id");

		if (!$result) {
			return false;
		}
		
		return true;
	}

	function update_product($product_id, $sku, $active, $id_category, $name, $image, $description, $price, $stock) {
		global $database_product;

		$result = $database_product->query("UPDATE product SET sku = '$sku', active = $active, id_category = $id_category, name = '$name', image = '$image',  description = '$description', price = $price, stock = $stock WHERE product_id = $product_id");	

		if (!$result) {
			return false;
		}
		
		return true;
	}

	function delete_category($category_id) {
		global $database_category;

		$result = $database_category->query("DELETE FROM category WHERE category_id = $category_id");

		if (!$result) {
			return "An error occurred while deleting the registration.";
		}
		else if ($database_category->affected_rows == 0) {
			return null;
		}
		else {
			return true;
		}
	}

	function delete_product($product_id) {
		global $database_product;

		$result = $database_product->query("DELETE FROM product WHERE product_id = $product_id");

		if (!$result) {
			return "An error occurred while deleting the registration.";
		}
		else if ($database_product->affected_rows == 0) {
			return null;
		}
		else {
			return true;
		}
	}
?>