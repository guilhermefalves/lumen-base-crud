# lumen-base-crud
The basic CRUD for Lumen MicroFramework

Pacote para servir como base para outros CRUDs em Lumen

---
## Installation
Para instalar o pacote, apenas execute: 
composer require guilhermefalves/lumen-base-crud

---
## Usage
É necessário que os Controllers do Lumen extenda a classe Controller deste projeto, ou seja:  
```
<?php
namespace App\Http\Controllers;

use LumenBaseCRUD\Controller as BaseCRUD;
use App\Models\User;

class UserController extends BaseCRUD
{
    protected $model = User::class;
    // ...
}
```

---
## Customization
Dentro da classe existem algumas variáveis para a customização, são elas:
* protected $model = ''; // O model utilizado
* protected array $postRules = []; // Regras de validação no verbo POST (default: [])
* protected array $putRules  = []; // Regras de validação no verbo PUT (default: [])

Também existem algumas funções que recebem um ponteiro para os dados e são executadas antes dos respectivos métodos:
* preShow
* preIndex
* preStore

E funções que recebem o objeto e são executadas após dos respectivos métodos:
* posStore
* preUpdate
* posUpdate
* preDelete
* posDelete

---
## Configuration
O pacote utiliza algumas configurações, são elas:
* database.pageSize - tamanho de cada página, utilizado nas funções show (default: 10)

---
## License
[License](https://github.com/guilhermefalves/lumen-base-crud/blob/master/LICENSE.md)