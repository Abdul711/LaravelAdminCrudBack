<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
        if (!backpack_user()->hasRole('admin')) {
            // Allow non-admins to only edit their own record
            CRUD::addClause('where', 'id', backpack_user()->id);

            // Disable create/delete access for non-admins
            $this->crud->denyAccess(['create', 'delete']);
        } else {
            CRUD::addClause('whereDoesntHave', 'roles', function ($query) {
                $query->where('name', 'admin');
            });
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
     public function updateCrud()
    {
        $request = $this->crud->getRequest();

        // Prevent updating password if it's blank
        if (!$request->filled('password')) {
            $request->request->remove('password');
        }

        return parent::updateCrud(); // ✅ Not `update()`, but `updateCrud()`
    }
//     public function storeCrud()
// {
//     $request = $this->crud->getRequest();

//     // Validate incoming data
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|unique:users,email',
//         'password' => 'required|string|min:8|confirmed',
//     ]);

//     // Hash password before saving
//     $validated['password'] = bcrypt($validated['password']);

//     // Create the user
//     $user = $this->crud->model->create($validated);

//     // Example: Assign default role
//     $user->assignRole('User');

//     \Alert::success('User created successfully')->flash();

//     return redirect($this->crud->route);


    protected function setupListOperation()
    {

        // $this->crud->set('show.setFromDb', false);
        $this->crud->addColumn([
            'name'  => 'S.No',
            'label' => '#',
            'type'  => 'model_function',
            'function_name' => 'getRowNumber',
            'orderable' => false,
        ]);

        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);
        // set fields from db columns.
        $this->crud->setOperationSetting('groupedErrors', false);
        $this->crud->setOperationSetting('inlineErrors', true);
        // CRUD::addField([
        //     'name' => 'pic',
        //     'label' => 'Profile Picture',
        //     'type' => 'upload',
        //     'upload' => true,
        // ]);



        $this->crud->addField([
            'name'  => 'name', // database column name
            'label' => 'User Name',
            'type'  => 'text',

        ]);
        $this->crud->addField([
            "name" => "email",
            "label" => "User Email",
            "type" => "text"
        ]);
        $this->crud->addField([
            "name" => "password",
            "label" => "User Password",
            "type" => "text"
        ]);
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }
   
    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */


    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public function assignedUser() {}
}
