<?php

namespace App\Http\Controllers\Admin;

use Alert;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use App\Enum\UserStatus;
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
        // User::saving(function ($model){

        // });

    }


    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupShowOperation()
    {
        // Prevent auto-loading from DB
        //   $this->crud->addClause('withCount', 'posts'); 
        // $this->crud->set('show.setFromDb', false);
        $this->crud->removeButton('show');   // removes "View"
        $this->crud->removeButton('edit');   // removes "Edit"
        $this->crud->removeButton('delete');
           $this->crud->removeButton('back');  // removes "Delete"
        $this->crud->removeAllButtons();
        $this->crud->set('show.setFromDb', true);
        $this->crud->addColumn(
            [
                'name'   => 'pic', // name of the db column
                'label'  => 'Photo', // table column heading
                'type'   => 'image',
                'prefix' => 'profilepic/', // relative to public/
                'height' => '60px',
                'width'  => '60px',
            ],
        );
        $this->crud->addColumn([
            "name" => "name",
            "label" => "User Name",
            "type" => "text"

        ]);
           $this->crud->addColumn([
        'name' => 'status',
        'label' => 'Status',
        'type'  => 'custom_html',
        'value' => function ($entry) {
          

            return '<span class="badge bg-' . $entry->status->badge() . '">' .
                   $entry->status->label() . '</span>';
        },
        'escaped' => false,
    ]);
        $this->crud->addColumn([
            "name" => "email",
            "label" => "User Email",
            "type" => "email"

        ]);

        $this->crud->addColumn([
            "name" => "title",
            "label" => "Job Title",
            "type" => "text"
        ]);

        $this->crud->addColumn([
            "name" => "title",
            "label" => "Job Title",
            "type" => "text"
        ]);
        $this->crud->addColumn([
            "name" => "postCount",
            "label" => "No Of Post",
            "type" => "number"
        ]);
        //   postCount
    }
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
        //    $this->crud->denyAccess('create');

        // Optional: Remove specific buttons from each row
        // $this->crud->removeButton('create'); // not necessary, but safe

        $this->crud->set('show.setFromDb', false);
        $this->crud->addClause('where', 'role', '!=', 'admin');
        $this->crud->addColumn([
            'name'  => 'S.No',
            'label' => '#',
            'type'  => 'model_function',
            'function_name' => 'getRowNumber',
            'orderable' => false,
        ]);
        $this->crud->addColumn(
            [
                'name'   => 'pic', // name of the db column
                'label'  => 'Photo', // table column heading
                'type'   => 'image',
                'prefix' => 'profilepic/', // relative to public/
                'height' => '60px',
                'width'  => '60px',
            ],
        );

        $this->crud->addColumns([
            [
                'name'  => 'name',
                'label' => 'Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => 'Email',
                'type'  => 'email',
            ],

            [
                'name'  => 'created_at',
                'label' => 'Creation Date',
                'type'  => 'date',

            ],
        ]);
//    $this->crud->addFilter([
//             'name'  => 'status',
//             'type'  => 'text',
//             'label' => 'Status',
//         ], UserStatus::options(), function ($value) {
//             $this->crud->addClause('where', 'status', $value);
//         });
$this->crud->addColumn([
    'name'  => 'status',
    'label' => 'Status',
    'type'  => 'custom_html',
    'value' => function ($entry) {
        return '<span class="badge bg-' . $entry->status->badge() . '">' .
            $entry->status->label() . '</span>';
    },
    'escaped' => false,
]);

// Inject custom HTML above the table

$this->crud->addColumn([
    'name'  => 'status',
    'label' => 'Status',
    'type'  => 'custom_html',
    'value' => function ($entry) {
        if (!$entry->status) {
            return '<span class="badge badge-secondary">Unknown</span>';
        }

        return '<span class="badge bg-' . $entry->status->badge() . '">' .
               $entry->status->label() .
               '</span>';
    },
    'escaped' => false,
]);
//  $this->crud->addButtonFromView('top', 'status_filter', 'crud.filters.status_filter', 'beginning');


 $this->crud->addButtonFromModelFunction('line', 'changeStatus', 'statusDropdownButton', 'beginning');
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

        CRUD::addField([
            'name'  => 'name',
            'label' => 'Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'email',
            'label' => 'Email',
            'type'  => 'email',
        ]);

        CRUD::addField([
            'name'  => 'password',
            'label' => 'Password',
            'type'  => 'password',
        ]);
        $this->crud->addField([
            'name'    => 'status',
            'label'   => 'Account Status',
            'type'    => 'radio',
            'options' =>UserStatus::options(),
            'default' => 'active',
            'inline'  => true, // if false, it will be vertical
        ]);
        CRUD::addField([
            'name'    => 'role',
            'label'   => 'Role',
            'type'    => 'select_from_array',
            'options' => ['admin' => 'Admin', 'user' => 'User'],
            'default' => 'user',
        ]);
         CRUD::addField([
            'name'    => 'title',
            'label'   => 'Title',
            'type'    => 'select_from_array',
            'options' => ['actress' => 'Actress',"influencer"=>"Influencer"],
            'default' => 'actress',
        ]);
        CRUD::addField([
            'name' => 'pic',
            'label' => 'Profile Picture',
            'type' => 'upload',
            'upload' => true,
        ]);
        // set fields from db columns.

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
    public function updateCrud()
    {
        $request = $this->crud->getRequest();

        // Prevent updating password if it's blank
        if (!$request->filled('password')) {
            $request->request->remove('password');
        }

        return parent::updateCrud(); // ✅ Not `update()`, but `updateCrud()`
    }
    public function changeStatus($id)
{
    $user = \App\Models\User::findOrFail($id);

    // Toggle between 'active' and 'inactive'
    $user->status = $user->status === 'active' ? 'deactive' : 'active';
    $user->save();

    Alert::success("Status changed to {$user->status} successfully")->flash();

    return redirect()->back();
}

public function setStatus($id, $status)
{
    $validStatuses = UserStatus::values();

    if (!in_array($status, $validStatuses)) {
        abort(400, 'Invalid status');
    }

    $user = \App\Models\User::findOrFail($id);
    $user->status = $status;
    $user->save();
   Alert::success("Status change successfully")->flash();


    return redirect()->back();
}
}
