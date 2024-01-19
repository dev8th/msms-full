<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <h1>Rekam Jejak</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <table id="table-history" class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th style="width:100px">User</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/historylist.js?v='.env('APP_VERSION'))}}"></script>