
<div id="container">
    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">



            <div class="toolbar type-button">
                <div class="c50l">
                    <h3 style="color:#f00">运费规则设置</h3>
                </div>
            </div>


            <div class="info view_form">
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
                <p>
                    <a href="<?=base_url()?>post_setting/add">增加规则</a>
                </p>
                <table width="100%">
                    <!--<col width="50%">
                    <col width="50%">-->
                    <tr>
                        <th>省份</th>
                        <th>城市</th>
                        <th>首重重量</th>
                        <th>续重单位重量</th>
                        <th>起步价</th>
                        <th>续重价</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <? $n = 0; ?>
                    <? if(!empty($rules)) {?>
                        <? foreach($rules as $k => $v){ ?>
                            <? $n ++; ?>
                            <tr class="<?=$n%2==0?"even":"odd";?>">
                                <td><?=getStrProvinceName($v->province_id)?></td>
                                <td><?=getStrCityName($v->city_id)?></td>
                                <td>
                                    <?=$v->first_weight?> g
                                </td>
                                <td>
                                    <?=$v->additional_weight?> g
                                </td>
                                <td>
                                    <?=cny($v->first_pay)?>
                                </td>
                                <td>
                                    <?=cny($v->additional_pay)?>
                                </td>
                                <td><a href="<?=base_url()?>post_setting/edit/<?=$v->id;?>">编辑</a></td>
                                <? if($v->province_id != 0) { ?>
                                <td><a href="<?=base_url()?>post_setting/delete/<?=$v->id;?>">刪除</a></td>
                                <? } else {?>
                                    <td></td>
                                <!--<td>此项必须保留(默认项)，请根据实际情况编辑</td>-->
                                <? } ?>
                            </tr>
                        <? } ?>
                    <? } ?>
                </table>


            </div>






        </div>
        <!-- IE Column Clearing -->
        <div id="ie_clearing">&nbsp;</div>
    </div>
