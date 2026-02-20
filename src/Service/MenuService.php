<?php

declare(strict_types=1);

namespace App\Service;

use WiQ\Sdk\GreatFoodSdk;
use WiQ\Sdk\Domain\DTO\Request\UpdateProductRequest;
use WiQ\Sdk\Domain\Exceptions\SdkException;

class MenuService
{
    public function __construct(
        private readonly GreatFoodSdk $sdk
    ) {}

    public function processTakeawayMenu(): void
    {
        try {
            echo "--- Scenario 1: Processing Takeaway Menu ---\n";

            $menus = $this->sdk->listMenus();

            $takeaway = null;
            foreach ($menus as $menu) {
                if ($menu->name === 'Takeaway') {
                    $takeaway = $menu;
                    break;
                }
            }

            if (!$takeaway) {
                echo "Menu 'Takeaway' not found.\n";
                return;
            }

            echo "Found Menu: {$takeaway->name} (ID: {$takeaway->id})\n";

            $products = $this->sdk->listProducts($takeaway->id);

            echo "Products in 'Takeaway':\n";
            foreach ($products as $product) {
                echo "- [ID: {$product->id}] {$product->name}\n";
            }

        } catch (SdkException $e) {
            echo "SDK Error in Scenario 1: " . $e->getMessage() . "\n";
        }
    }

    public function updateSpecificProduct(int $menuId, int $productId, string $newName): void
    {
        try {
            echo "\n--- Scenario 2: Updating Product ---\n";

            $request = new UpdateProductRequest(name: $newName);

            $success = $this->sdk->updateProduct($menuId, $productId, $request);

            if ($success) {
                echo "Product #{$productId} successfully updated to '{$newName}'.\n";
            } else {
                echo "Failed to update product #{$productId}.\n";
            }

        } catch (SdkException $e) {
            echo "SDK Error in Scenario 2: " . $e->getMessage() . "\n";
        } catch (\InvalidArgumentException $e) {
            echo "Validation Error: " . $e->getMessage() . "\n";
        }
    }
}
