@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Categories Hierarchy</label>
            <div class="float-right">
                <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-list"></i> List View
                </a>
                <a href="#" data-toggle="modal" data-target="#create" class="btn btn-success btn-sm">
                    <i class="fa fa-plus"></i> Add Category
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0">
                                <i class="fa fa-info-circle text-primary"></i>
                                This view displays your categories in a hierarchical tree.
                                <strong>Parent categories</strong> are displayed at the top level, with their
                                <strong>subcategories</strong> nested underneath.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($parentCategories->count() > 0)
                <div class="category-tree-container">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Category Tree -->
                            @foreach($parentCategories as $parentCategory)
                                <div class="category-node parent-node mb-3">
                                    <div class="node-content">
                                        <div class="node-header">
                                            <div class="node-info">
                                                <i class="fa fa-folder text-warning category-icon"></i>
                                                <span class="category-name">{{ $parentCategory->name }}</span>
                                                @if($parentCategory->status == 'inactive')
                                                    <span class="badge badge-danger ml-2">Inactive</span>
                                                @else
                                                    <span class="badge badge-success ml-2">Active</span>
                                                @endif
                                                @if($parentCategory->subcategories && $parentCategory->subcategories->count() > 0)
                                                    <span class="badge badge-info ml-2">{{ $parentCategory->subcategories->count() }} subcategories</span>
                                                @endif
                                            </div>
                                            <div class="node-actions">
                                                <a href="{{ route('categories.show', $parentCategory->id) }}" class="btn btn-sm btn-outline-info action-btn" title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-primary action-btn" data-toggle="modal" data-target="#edit-{{ $parentCategory->id }}" title="Edit Category">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if(!$parentCategory->hasChildren())
                                                    <form id="deleteForm-{{ $parentCategory->id }}"
                                                        action="{{ route('categories.destroy', $parentCategory->id) }}" method="POST"
                                                        class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn action-btn" title="Delete Category"
                                                            data-form-id="deleteForm-{{ $parentCategory->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary action-btn" disabled title="Cannot delete - has subcategories">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if($parentCategory->description)
                                            <div class="node-description">
                                                {{ Str::limit($parentCategory->description, 100) }}
                                            </div>
                                        @endif
                                    </div>

                                    @if($parentCategory->subcategories && $parentCategory->subcategories->count() > 0)
                                        <div class="subcategories-container">
                                            <div class="vertical-line"></div>
                                            <div class="subcategories-list">
                                                @foreach($parentCategory->subcategories as $subcategory)
                                                    <div class="category-node child-node">
                                                        <div class="node-content">
                                                            <div class="node-header">
                                                                <div class="node-info">
                                                                    <i class="fa fa-level-down-alt text-muted connector-icon"></i>
                                                                    <i class="fa fa-tag text-info category-icon"></i>
                                                                    <span class="category-name">{{ $subcategory->name }}</span>
                                                                    @if($subcategory->status == 'inactive')
                                                                        <span class="badge badge-danger ml-2">Inactive</span>
                                                                    @else
                                                                        <span class="badge badge-success ml-2">Active</span>
                                                                    @endif
                                                                </div>
                                                                <div class="node-actions">
                                                                    <a href="{{ route('categories.show', $subcategory->id) }}" class="btn btn-sm btn-outline-info action-btn" title="View Details">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                    <a href="#" class="btn btn-sm btn-outline-primary action-btn" data-toggle="modal" data-target="#edit-{{ $subcategory->id }}" title="Edit Category">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    @if(!$subcategory->hasChildren())
                                                                        <form id="deleteForm-{{ $subcategory->id }}"
                                                                            action="{{ route('categories.destroy', $subcategory->id) }}" method="POST"
                                                                            class="d-inline-block">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="button" class="btn btn-outline-danger btn-sm delete-btn action-btn" title="Delete Category"
                                                                                data-form-id="deleteForm-{{ $subcategory->id }}">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @else
                                                                        <button class="btn btn-sm btn-outline-secondary action-btn" disabled title="Cannot delete - has subcategories">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @if($subcategory->description)
                                                                <div class="node-description">
                                                                    {{ Str::limit($subcategory->description, 80) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> No categories found. Please create a category to get started.
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    @include('backends.categories.create', ['categories' => \App\Models\Category::all()])

    <!-- Edit Modals -->
    @foreach(\App\Models\Category::all() as $category)
        @include('backends.categories.edit', ['category' => $category, 'categories' => \App\Models\Category::where('id', '!=', $category->id)->get()])
    @endforeach

    <!-- Script for delete confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const formId = this.getAttribute('data-form-id');
                    if (confirm('Are you sure you want to delete this category?')) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });
    </script>

    <style>
        /* Tree View Styling */
        .category-tree-container {
            padding: 10px 0;
        }

        .category-node {
            position: relative;
            margin-bottom: 10px;
        }

        .node-content {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px 15px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .parent-node > .node-content {
            background-color: #f8f9fa;
            border-left: 4px solid #ffc107;
        }

        .child-node > .node-content {
            background-color: #ffffff;
            border-left: 4px solid #17a2b8;
            margin-left: 20px;
        }

        .node-content:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .node-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .node-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .node-actions {
            display: flex;
            gap: 5px;
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .node-content:hover .node-actions {
            opacity: 1;
        }

        .category-icon {
            font-size: 1.1rem;
            margin-right: 8px;
        }

        .connector-icon {
            margin-right: 5px;
        }

        .category-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .node-description {
            color: #666;
            font-size: 0.85rem;
            margin-top: 5px;
            margin-left: 25px;
        }

        .subcategories-container {
            position: relative;
            margin-top: 10px;
        }

        .vertical-line {
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }

        .subcategories-list {
            padding-left: 40px;
        }

        .action-btn {
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>
@endsection
