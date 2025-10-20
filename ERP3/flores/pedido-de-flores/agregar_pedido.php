<div class="row p-0 d" id="father">
    <div class="col-12 col-md-7 col-sm-12 col-lg-5 " id="divCarritoCompras">
        <div class="mt-3" id="content-notas"></div>
        <div class="rowx">
            <div class="col-md-12 col-lg-12 content-foliox" id="pedido-table"></div>
        </div>
    </div><!-- content ticket-->
    <div class="col-12 col-md-5 col-sm-12 col-lg-7  " id="divProductosLista">
        <div class="row">
            <div class="col-sm-2">
                <input type="hidden" class="form-control" value="83" id="txtFolio">
            </div>
        </div>

        <div class="row mt-20">
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-3">
                <select onchange="list_sub()" class="form-select" id="txtCat">
                    <option value="0">-- Seleccciona --</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-3">
                <select onchange="list_productos()" class="form-select" id="txtSubCat">
                    <option value="0">-- Seleccciona --</option>
                </select>
            </div>
        </div>
        <div id="content-productos-1"></div>
    </div>
</div><!-- -->