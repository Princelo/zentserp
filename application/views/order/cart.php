
<div id="container">



<!-- begin: #col1 - first float column -->
<div id="col1" role="complementary" style="display: block;">
    <div id="col1_content" class="clearfix">

        <ul id="left_menu">
            <li>
                <a href='<?=base_url()?>order/listpage' ><div>订单列表 </div></a>
            </li>
            <li>
                <a href='<?=base_url()?>order/index_sub' ><div>下级代理订单查询 </div></a>
            </li>
            <li>
                <a href="<?=base_url()?>order/cart"><div>我的购物车</div></a>
            </li>
        </ul>
    </div>
</div>
<!-- end: #col1 -->
<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
<div id="col3_content" class="clearfix">



    <div class="toolbar type-button">
        <script>
            if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                alert("<?=$this->session->flashdata('flashdata', 'value');?>");
        </script>
        <div class="c50l">
            <h3></h3>
        </div>
    </div>


    <fieldset>
        <legend>我的购物车 </legend>

        <table width="100%">
            <!--<col width="50%">
            <col width="50%">-->
            <tr>
                <th><input type="checkbox" id="selectAll" onclick="" checked="checked"> 全选</th>
                <th>商品</th>
                <th>单价</th>
                <th>数量</th>
                <th>小计</th>
                <th>操作</th>
            </tr>
            <? $n = 0; ?>
            <? $total = 0; ?>
            <? $item = ""; ?>
            <? if(!empty($products)) {?>
                <? foreach($products as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><input type="checkbox" id="product-<?=$v->pid?>" class="product-item" pid="<?=$v->pid?>" checked="checked"> </td>
                        <td><a href="<?=base_url()?>product/details/<?=$v->pid?>"><?=$v->title;?>(<?=$v->pid?>)</a></td>
                        <td><input type="hidden" value="<?=money($v->unit_price)?>" class="unit-price" id="price-<?=$v->pid?>"><?="￥".money($v->unit_price);?></td>
                        <td>
                            <div class="buy-quantity">
                                <input value="<?=$v->quantity;?>" class="quantity" pid="<?=$v->pid?>" id="quantity-<?=$v->pid?>"/>
                                <a href="javascript:void(0)" class="increase" onclick="increase(<?=$v->pid?>)" pid="<?=$v->pid?>">+</a>
                                <a href="javascript:void(0)" class="decrease" onclick="decrease(<?=$v->pid?>)" pid="<?=$v->pid?>">-</a>
                            </div>
                        </td>
                        <td>
                            ￥<span class="total" pid="<?=$v->pid?>" id="total-<?=$v->pid?>"><?=bcmul($v->quantity, money($v->unit_price), 2)?></span>
                        </td>
                        <? $total = bcadd($total, bcmul($v->quantity, money($v->unit_price), 2), 2); ?>
                        <? $item .= "|" . $v->pid . "," . $v->quantity; ?>
                        <td>
                            <a href="<?=base_url()?>order/remove_from_cart/<?=$v->pid?>">刪除</a>
                        </td>
                    </tr>
                <? } ?>
            <? } ?>
        </table>
        <p class="global-total">
            <strong>合计(不含运费):</strong>
            ￥<span id="global-total"><?=money($total)?></span>
            <div style="color:red; font-weight:bold; font-size:18px; display:none;" id="tips">你当前等级为零售商, 购物车金额需满￥1980才能完成交易</div>
        </p>

    </fieldset>


    <form action="<?=base_url()?>order/add_by_cart" method="post">
        <input value="<?=substr($item, 1)?>" id="select-items" type="hidden" name="items" />
        <div class="toolbar type-button">
            <div class="c50l">
                <input type="submit" name="btnSubmit" id="btnS" value="下一步，填写入货信息 "  />			</div>
            <div class="c50r right">
            </div>
        </div>
    </form>



</div>
<!-- IE Column Clearing -->
<div id="ie_clearing">&nbsp;</div>
<!--
        <script>
            $(document).ready(function(){
                Calendar.setup({
                    weekNumbers   : true,
                    fdow		: 0,
                    inputField : 'end_time',
                    trigger    : 'end_time-trigger',
                    onSelect   : function() { this.hide() }
                });

            });

        </script>

    : IE Column Clearing -->
</div>
    <script>
        Array.prototype.contains = function(obj) {
            var i = this.length;
            while (i--) {
                if (this[i] == obj) {
                    return true;
                }
            }
            return false;
        }
        var addSelectedItems = function(id, value){
            value_arr = value.split('|');
            value_arr_without_quantity = [];
            for(var i = value_arr.length - 1; i >= 0; i --)
            {
                value_arr_without_quantity[i] = value_arr[i].substring(value_arr[i], value_arr[i].indexOf(','));
            }
            if(value_arr_without_quantity.contains(id))
                return value;
            else if(value == "")
                return id + "," + $("#quantity-"+id).val();
            else
                return value + "|" + id + "," + $("#quantity-"+id).val();
        }
        var addSelectedItemsForCalc = function(id, value){
            value_arr = value.split('|');
            value_arr_without_quantity = [];
            for(var i = value_arr.length - 1; i >= 0; i --)
            {
                value_arr_without_quantity[i] = value_arr[i].substring(value_arr[i], value_arr[i].indexOf(','));
            }
            if(value_arr_without_quantity.contains(id))
                return value;
            else if(value == "")
                return id + "," + $("#quantity-"+id).val();
            else
                return value + "|" + id + "," + $("#quantity-"+id).val();
        }
        var removeSelectedItems = function(id, value){
            value_arr = value.split('|');
            for(var i = value_arr.length - 1; i >= 0; i--) {
                if(value_arr[i].substring(value_arr[i], value_arr[i].indexOf(',')) == id) {
                    value_arr.splice(i, 1);
                }
            }
            $("#selectAll").prop('checked', false);
            return value_arr.join("|");
        }
        var removeSelectedItemsForCalc = function(id, value){
            value_arr = value.split('|');
            for(var i = value_arr.length - 1; i >= 0; i--) {
                if(value_arr[i].substring(value_arr[i], value_arr[i].indexOf(',')) == id) {
                    value_arr.splice(i, 1);
                }
            }
            return value_arr.join("|");
        }
        var updateTotal = function(){
            $("#select-items").val(removeSelectedItems($(this).attr('pid'), $("#select-items").val()));
            $("#select-items").val(addSelectedItems($(this).attr('pid'), $("#select-items").val()));
            value_arr = $('#select-items').val().split("|");
            value_arr_without_quantity = [];
            for(var i = value_arr.length - 1; i >= 0; i --)
            {
                value_arr_without_quantity[i] = value_arr[i].substring(value_arr[i], value_arr[i].indexOf(','));
            }
            var total = 0;
            $('.total').each(function(){
               if(value_arr_without_quantity.contains($(this).attr('pid')))
               {
                   total += parseFloat($(this).html());
                   total.toFixed(2);
               }
            });
            $("#global-total").html(total.toFixed(2) + "");
            if(<?=$level?> == 0)
            {
                if(total < 1980){
                    $('#tips').show();
                    $('#btnS').hide();
                }else{
                    $('#tips').hide();
                    $('#btnS').show();
                }
            }
        }
        $("#selectAll").change(function(){
            if(this.checked)
            {
                $('input[type="checkbox"]').each(function(){
                    this.checked = true;
                    if($(this).attr('pid') != undefined)
                        $("#select-items").val(addSelectedItems($(this).attr('pid'), $("#select-items").val()));
                    check();
                });
            }else{
                $('input[type="checkbox"]').each(function(){
                    this.checked = false;
                    if($(this).attr('pid') != undefined)
                        $("#select-items").val(removeSelectedItems($(this).attr('pid'), $("#select-items").val()));
                    check();
                });
            }
            updateTotal();
        });
        $(".product-item").each(function(){
            $(this).change(
                function(){
                    if(this.checked){
                        $("#select-items").val(addSelectedItems($(this).attr('pid'), $("#select-items").val()));
                        updateTotal();
                        check();
                    }else{
                        $("#select-items").val(removeSelectedItems($(this).attr('pid'), $("#select-items").val()));
                        updateTotal();
                        check();
                    }
                }
            );
        });
        $(".quantity").each(function(){
            $(this).change(function(){
                $("#select-items").val(removeSelectedItems($(this).attr('pid'), $("#select-items").val()));
                $("#select-items").val(addSelectedItems($(this).attr('pid'), $("#select-items").val()));
                pid = $(this).attr('pid');
                $("#total-"+pid).html( (parseFloat($('#price-'+pid).val()) * parseInt($(this).val())).toFixed(2) );
                updateTotal();
                check();
            });
        });
        function finishConfirm()
        {
            if (confirm("确认执行？")){
                $("form").submit();
            } else {

            }
        }
        var increase = function(id)
        {
            $("#quantity-"+id).val(parseInt($("#quantity-" + id).val()) + 1);
            $("#total-"+id).html( (parseFloat($('#price-'+id).val()) * parseInt($("#quantity-"+id).val())).toFixed(2) );
            $("#select-items").val(removeSelectedItemsForCalc(id, $("#select-items").val()));
            if($("#product-"+id).is(':checked'))
                $("#select-items").val(addSelectedItemsForCalc(id, $("#select-items").val()));
            updateTotal();
            check();
        }
        var decrease = function(id)
        {
            if($("#quantity-" + id).val() > 1){
                $("#quantity-"+id).val(parseInt($("#quantity-" + id).val()) - 1);
                $("#total-"+id).html( (parseFloat($('#price-'+id).val()) * parseInt($("#quantity-"+id).val())).toFixed(2) );
                $("#select-items").val(removeSelectedItemsForCalc(id, $("#select-items").val()));
                if($("#product-"+id).is(':checked'))
                    $("#select-items").val(addSelectedItemsForCalc(id, $("#select-items").val()));
            }
            updateTotal();
            check();
        }
        var check = function()
        {
            if(<?=$level?> == 0)
            {
                console.log(parseFloat($('#global-total').html()));
                if(parseFloat($('#global-total').html()) < 1980){
                    $('#tips').show();
                    $('#btnS').hide();
                }else{
                    $('#tips').hide();
                    $('#btnS').show();
                }
            }
        }
        check();
    </script>
