---
title: RunAsRoot Product Priorities
keywords: run_as_root, priority, bestseller, grid, form
module_name: RunAsRoot_ProductPriorities
author: David Lambauer, Denys Sliepnov
send_questions_to: david@run-as-root.sh
category: Marketing
---

## General

Provides a product priorities column inside Admin Products Grid.

## Installations

```
composer require run_as_root/ext-magento2-product-priorities
bin/magento setup:upgrade
```

## Features

### 'Priority' column inside 'Products' grid

`Priority` column includes the proportion value of relevance for each product.
These values should match the Priority Matrix.

`Priority Matrix`

| Priority name | Relevance proportion value % | Cell background color |
|:--------------|:-----------------------------|:----------------------|
| A             | 90                           | green                 |    
| B             | 80                           | light-green           |
| ...           | ...                          | ...                   |
| F             | 0                            | red                   |

`Priority Matrix` is editable (data stores at the database inside `run_as_root_product_priorities` table).
There are initial values. `Priority Name` and `relevance proportion value %` fields must be unique.

Admin can adjust priority for a specific period of time.

Priority value calculates by the next formula:

1. report data loads for the specific period of time from the table `sales_bestsellers_aggregated_daily`
2. system finds the product with the greatest `ordered_qty` value and marks the target product as top bestseller (takes
   the highest priority value from the matrix, i.e. `A`)
3. system walks through other products and calculates priority value by the formula:
    4. `targetProduct['ordered_qty'] * 100 / topBestsellerProduct['ordered_qty'] = targetProduct['proportion_value']`
    5. system fetches the max `relevance proportion value` from the `Priority Matrix` that is less than
       `targetProduct['proportion_value']`

## Technical Specification

### Api

#### `RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface`

Defines all the getters and setters for the Product Priority entity.

#### `RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface`

Gives access to persistent data entities. Have the following methods:

- `save(ProductPriorityInterface $productPriority)`: Creates a new record if no id present, otherwise updates an
  existing record with the specified id.
- `get(int $entityId)`: Performs a database lookup by id and returns a data entity interface
- `delete(ProductPriorityInterface $productPriority)`: Deletes the specified entity
- `deleteById(int $entityId)`: Deletes the specified entity by id

### Block

#### `RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons\GenericButton`

Implements shared methods that are used inside Product Priority Form.

- `getEntityId()`: returns an id for the specific entity
- `getUrl($route = '', $params = [])`: implements urlBuilder wrapper

#### `RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons\BackButton`

Implements back action for button if admin on the Product Priority Form page.

#### `RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons\DeleteButton`

Implements delete action for button if admin on the Product Priority Form page.

#### `RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons\SaveButton`

Provides save action data for the Split Save button on the Product Priority Form page.

#### `RunAsRoot\ProductPriorities\Block\Adminhtml\System\Config\Form\Field\Date`

Implements DatePicker frontend_model for System Config.

### ConfigProvider

#### `RunAsRoot\ProductPriorities\ConfigProvider\General`

Implements the functionality of fetching module configurations.

- `isModuleEnabled(int $storeId)`: returns if module is Enabled/Disabled
- `getFromDate(int $storeId)`: returns start date of the period

### Controller

#### `RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priorities`

Returns the Product Priorities Grid page

#### `RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\MassDelete`

Handles the mass delete Product Priority entities on the Product Priorities Grid

#### `RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\AbstractPriority`

Implements shared methods that are used by Controllers (Product Priority Form).

#### `RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priority\*`

Implements the CRUD actions

### DataProvider

#### `RunAsRoot\ProductPriorities\DataProvider\Priority`

Implements the calculation process and data provider for the 'Priority' column
- `getData()`: returns the processed data
- `prepareCollection()`: initiates and adjusts bestseller products collection
- `getPriorityByProportion(int $proportionValue)`: returns the match value from the Matrix
- `getPriorities()`: returns the Matrix data

### Model

#### `RunAsRoot\ProductPriorities\Model\ProductPriority\DataProvider`

Implements data provider for Product Priority entity on the Create/Edit Form

#### `RunAsRoot\ProductPriorities\Model\*`

Implement entity related models, resource model, collection, repository

### Setup

#### `RunAsRoot\ProductPriorities\Setup\Patch\Data\InsertSampleData`

Inserts the initial data to database (`Priority Matrix`)

### Ui

#### `RunAsRoot\ProductPriorities\Ui\Component\Listing\Columns\Actions`

Implements Edit/Delete actions for Product Priorities Grid

#### `RunAsRoot\ProductPriorities\Ui\Component\Listing\Columns\ProductPriority`

Describes the 'Priority' column class

## Configuration

| tab     | group   | section               | field     |
|:--------|:--------|:----------------------|:----------|
| run_as_root | general | Product Priorities | Enable    |
| run_as_root | general | Product Priorities | From Date |


## Product Priorities Grid

* Navigate to Marketing -> run_as_root -> Product Priorities