@include('css')
@include('layout.adminnav')
@if(Session::has('message'))
 <div class="col-md-12">
 <div class="alert alert-info">{{ Session::get('message') }}</div>
 </div>
@endif
<div class="container">
 <div class="col-md-12">
 <div class="pull-right">
<a href="/posts/create"><div class="btn btn-success">Add New Blog Post</div></a>
 </div>
 <table class=”table”>
 <thead>
 <tr>
 <th>ID</th>
 <th>BLOG TITLE</th>
 <th>EDIT BLOG</th>
 <th>DELETE BLOG</th>
 </tr>
 </thead>
 <tbody>
 <tr>
 <td>1</td>
 <td>Hello world</td>
 <td><div class=”btn btn-primary”>Edit Blog</div></td>
 <td><div class=”btn btn-danger”>Delete Blog</div></td>
 </tr>
 </tbody>
 </table>
 </div>
 </div>
@stop
@include('js')