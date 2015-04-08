
<div id="container">
<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">


        <div class="info view_form">
            <h2>臻芝ERP管理系统 </h2>
            <h4><?php echo validation_errors(); ?>
                <span class="red">
                    <?=$this->session->flashdata('flashdata', 'value');?>
                </span>
            </h4>
            <?php //phpinfo(); ?>
            <h4>编辑公告：</h4>
            <form action="<?=base_url()?>forecast/index" method="post" />
            <div>
                <textarea name="forecast" rows="30" cols="90"><?=$forecast?></textarea>
            </div>
            <input type="submit" value="修改"/>
            </form>
        </div>



        <div class="">
            <h2></h2>


        </div>



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
<!-- end: #col4 -->	</div>

<div id="footer">
    Copyright &copy; <?=date('Y');?> by UNVWEB<br/>
    All Rights Reserved.<br/>
</div><!-- footer -->
</body>
</html>