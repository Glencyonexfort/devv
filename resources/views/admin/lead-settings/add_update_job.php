<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/eliteadmin/node_modules/dropify/dist/css/dropify.min.css">
<script src='<?php echo base_url(); ?>/assets/system_design/fullCalendar/moment.min.js'></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/system_design/typeahead.js"></script>
<?php $this->load->view('admin/sales/script'); ?>
<?php if (isset($job_id) && $job_id != "") { ?>
    <script type="text/javascript">
        window.onload = function () {
            edit_job("<?php echo $job_id; ?>");
        };
    </script>
<?php } else { ?>
    <script type="text/javascript">
        window.onload = function () {
            book_job("<?php echo $template_id; ?>");
        };
    </script>
<?php } ?>
<link href="<?php echo base_url(); ?>/assets/eliteadmin/css/pages/tab-page.css" rel="stylesheet">
<style>
    .attachment-checkbox{
        float: left;margin-right: 10px;margin-top: 13px;height: 30px;width: 18px;
    }
    /* Always set the map height explicitly to define the size of the div
     * element that contains the map. */
    #map {
        /*height: 100%;*/
        height: 298px;/*320px;*/
        width: 100%;
    }
    #infowindow-content .title {
        font-weight: bold;
    }
    #infowindow-content {
        display: none;
    }
    #map #infowindow-content {
        display: inline;
    }
    .tab-pane { margin-top: 10px; }
    .tabs-left>.nav-tabs {
        float: left;
        /*margin-right: 19px;*/
        /*border-right: 1px solid #ddd;*/ }
    .tabs-left>.nav-tabs>li, .tabs-right>.nav-tabs>li { float: none; }
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
        color: white !important;
        cursor: default;
        background-color: #23527c !important;;
        border: 1px solid #ddd !important;;
        border-bottom-color: transparent !important;;
    }
    .moveType{
        float: left;
        margin: 4px 0px 3px 0px;
        font-size: 14px;
    }
    .additionalServices{
        float: left;
        margin: 5px 10px 5px 5px;
        font-size: 14px;
        width: 100%;
    }
    .right{
        float: right;
        margin-right: 10px;
        font-size: 14px;
        margin-top: 5px;
    }
    .borderStyle {
        background-color: white;
        margin-bottom: 10px;
        border: 1px solid darkgrey;
        padding-bottom: 10px;
    }
    .margin-bottom-8{
        margin-bottom: 8px;
    }
    .margin-bottom-25{
        margin-bottom: 25px;
    }
    hr {  border-top: 1px dotted #C0C0C0 !important; }
    .instructions{
        color: red;
    }
    #job_logs,#job_logs_notes, #job_logs_messages {
        list-style: none;
        font-size: 14px;
    }
    #job_logs_messages {
        list-style: none;
        font-size: 14px;
        padding-left: 0px;
    }
    #job_logs{
        float: left;
        padding: 0px;
        max-height: 900px;
        overflow-y: scroll;
    }
    .logs{
        width: 100%;float: left;border-bottom: 1px dotted darkgray;padding: 10px;
    }
    .log_detail{
        float: left;/*margin-left: 10px;*/width: 100%;
    }
    .log_date{
        float: right;/*margin-left: 10px;*/color: gray;font-size: 12px;  margin-top: 3px;
    }
    .log_createdBy{
        float: left;/*margin-left: 10px;*/color: gray;font-size: 12px;  margin-top: 3px;
    }
    .log_icon{
        height: 32px !important;width: 32px !important;
    }
    .log_title{
        float: left;margin-left: 10px;margin-top: 5px; font-size: 20px;
    }
    .avatar_icon{
        height: 20px !important;width: 20px !important;margin-right: 4px;
    }
    .image-upload > input {
        visibility:hidden;
        width:0;
        height:0
    }
    .customInputFile{
        display: block;
        margin: 0 0 10px 0;
        cursor: pointer;
    }
    .customInputFile input[type="file"] {
        visibility:hidden;
    }
    .customInputFile #fileUpdate {
        background: #23527c;
        display: block;
        padding: 25px 0;
        color: rgb(255,255,255);
        text-align: center;
        overflow: auto;
    }
    .customInputFile #fileUpdate #fileUpdate-left{
        display: inline-block;
        width: 190px;
        font-size: 20px;
        line-height: 23px;
        text-transform: uppercase;
        text-align: left;
    }
    #progress-wrp {
        border: 1px solid #0099CC;
        padding: 1px;
        position: relative;
        border-radius: 3px;
        /*margin: 10px;*/
        text-align: left;
        background: #fff;
        box-shadow: inset 1px 3px 6px rgba(0, 0, 0, 0.12);
    }
    #progress-wrp .progress-bar{
        height: 20px;
        border-radius: 3px;
        background-color: #3CB371;
        width: 0;
        box-shadow: inset 1px 1px 10px rgba(0, 0, 0, 0.11);
    }
    #progress-wrp .status{
        top:3px;
        left:50%;
        position:absolute;
        display:inline-block;
        color: #000000;
    }
    /*#tallModal */
    #attachments::-webkit-scrollbar-track,#job_logs::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 10px;
        background-color: #F5F5F5;
    }
    #attachments::-webkit-scrollbar,#job_logs::-webkit-scrollbar
    {
        width: 12px;
        background-color: #F5F5F5;
    }
    #attachments::-webkit-scrollbar-thumb,#job_logs::-webkit-scrollbar-thumb
    {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
        background-color: #e46a76;
    }
    .lightGray-border{
        border: 1px solid gainsboro;
    }
    .left{
        float: left;
    }
    /*.btn-success {
        color: #fff !important;
      }
    a {
        color: #337ab7 !important;
        text-decoration: none !important;
      }*/
    /*.fix-header{
      position: fixed;width: 95.5%;z-index: 9999;top: 31%;
    }
    .fix-header-top{
      position: fixed;width: 95.5%;z-index: 9999;top: 10%;
    }
    .fixed-ul{
      position: fixed;width: 70%;z-index: 99999;float: left;top: 41%;
    }
    .fixed-ul-top{
      position: fixed;width: 70%;z-index: 99999;float: left;top: 18.9%;background-color: beige;
    }*/
    .requiredField{
        color:red;
    }
    #dispatchTable td{
        padding: 8px !important;
    }
    .checkbox-style{
        width: 21px;
        height: 17px;
        margin-right: 5px;
        float: left;
    }
</style>
<!-- ============================================================== -->
<!-- Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<div class="row page-titles">
    <!-- <div id="action-message" style="display: none;float:left;margin-left:40px;margin-top:5px;width: 80%;" class="alert">
        <a href="#" class="close" data-dismiss="alert">×</a>
    </div> -->
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"><?php if (isset($title)) echo $title; ?></h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo site_url(); ?>/auth"><?php echo $this->lang->line('home'); ?></a></li>
                <li class="breadcrumb-item"><a href="javascript:">Sales</a></li>
                <li class="breadcrumb-item active"><?php
                    /* echo $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; */
                    if (isset($title))
                        echo $title;
                    ?></li>
            </ol>
            <!-- <button type="button" class="btn btn-info d-none d-lg-block m-l-15"><i class="fa fa-plus-circle"></i> Create New</button> -->
        </div>
    </div>
</div>
<?php echo $this->session->flashdata('message'); ?>

<div class="row">
    <div class="col-lg-12">
        <?php
        $attributes = array('id' => 'add_new_job');
        echo form_open("admin/add_new_job_popup", $attributes)
        ?>
        <div class="card">
            <div class="card-header bg-danger fix-header" id="book-job-header">
                <h4 class="m-b-0 text-white" style="float: left;margin-top: 4px;" id="modal-title">Book Job</h4>
                <h4 class="m-b-0 text-white" id="top-cbm-div" style="text-align: center;margin-top: 4px;display: none;">CBM: </h4>
                <!-- <span class="card-header bg-secondary" style="margin-left: 10px;border-radius: 8px;"><button type="submit" id="submit_job_btn" class="btn waves-effect waves-light btn-outline-danger submit_job_btn"> <i class="fa fa-check"></i> <?php //echo $this->lang->line('make_a_booking');   ?></button>
                        <a class="btn waves-effect waves-light btn-outline-danger" href="<?php //echo site_url();   ?>/admin/<?php //echo $page_name;    ?>"><i class="fa fa-arrow-left"></i>  Go Back</a></span> -->

                <span class="card-header" style="position: fixed;left: 0.2%;padding: 0px;top: 57%;">
                    <button type="submit" class="btn waves-effect waves-light btn-outline-danger" style="display: block;margin-bottom: 5px;padding: 4px 7px 3px 9px;" data-toggle="tooltip" data-placement="top" title="Save Booking"> <i class="fa fa-check"></i> </button>

                    <a style="display: block;padding:4px 7px 3px 7px;"  data-toggle="tooltip" data-placement="bottom" title="Go Back" class="btn waves-effect waves-light btn-outline-danger" href="<?php echo site_url(); ?>/admin/<?php echo $page_name; ?>"><i class="fa fa-arrow-left"></i> </a></span>

            </div>
            <div class="card-body">  <!-- stye="margin-top: 40px;"   -->
                <div class="row show-grid">
                    <div class="col-xs-12 col-md-9 p-0" style="border: 1px solid gray;">
                        <div class="col-md-12 p-0">
                            <div class="card p-0">
                                <!-- <div class="card-header bg-secondary">

                                </div> -->
                                <div class="card-body p-0">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs customtab2 fixed-ul" role="tablist"  data-spy="affix" data-offset-top="205">
                                        <li class="nav-item" id="SummaryTabLi" style="display: none;"> <a class="nav-link" data-toggle="tab" href="#SummaryTab" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Summary</span></a> </li>

                                        <li class="nav-item"> <a class="nav-link" id="jobDetails" data-toggle="tab" href="#home" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Job Details</span></a> </li>

                                        <li class="nav-item" id="DispatchTabLi" style="display: none;"> <a class="nav-link" data-toggle="tab" href="#DispatchTab" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Dispatch</span></a> </li>

                                        <li class="nav-item" id="calculatorTabLi" style="display: block;"> <a class="nav-link" data-toggle="tab" href="#calculatorTab" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Inventory</span></a> </li>

                                        <li class="nav-item" id="invoicingTabLi" style="display: none;"> <a class="nav-link" data-toggle="tab" href="#invoicingTab" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Invoicing</span></a> </li>

                                        <li class="nav-item" id="emailTabLi" style="display: none;"> <a class="nav-link" data-toggle="tab" href="#emailTab" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Email</span></a> </li>

                                        <li class="nav-item" id="attachmentTabLi" style="display: none;"> <a class="nav-link" data-toggle="tab" href="#attachmentTab" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Upload File</span></a> </li>

                                        <li class="nav-item" id="JobLogTabLi" style="display: none;"> <a class="nav-link" data-toggle="tab" href="#JobLogTab" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">SMS/Notes</span></a> </li>

                                        <li role="presentation" id="EmailLogTabLi" style="display: none;"><a class="nav-link" href="#EmailLogTab" aria-controls="JobLogTab"role="tab" data-toggle="tab">Email Log</a>
                                        </li>

                                        <li role="presentation" id="SMSLogTabLi" style="display: none;"><a class="nav-link" href="#SMSLogTab" aria-controls="JobLogTab"role="tab" data-toggle="tab">SMS Log</a>
                                        </li>

                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content tabcontent-border">
                                        <div id='ajax_loader' style="position: fixed; left: 43%; top: 50%; display: none;z-index:9999;">
                                            <img src="<?php echo base_url(); ?>/assets/system_design/images/loading-spinner.svg" style="border:0px;"></img>
                                        </div>

                                        <!-- Start of Summary Tab -->
                                        <div class="tab-pane" id="SummaryTab" role="tabpanel">
                                            <?php $this->load->view('admin/sales/job_summary'); ?>
                                            <!-- <div class="col-md-12">
                                            <table>
                                               <tr style="background-color: #dddddd;">
                                                 <th style="width: 70%"><?php echo '<h3>' . $job_data->company_name . '</h3>'; ?></th>
                                                 <th style="width: 30%"><?php echo 'ABN:' . $job_data->company_abn; ?></th>
                                               </tr>
                                               <tr>
                                                 <td colspan="2"><strong>Address:</strong> <?php echo $job_data->company_address; ?></td>
                                               </tr>
                                             </table>

                                           </div>-->
                                        </div>
                                        <!-- End of Summary Tab -->


                                        <!-- Home/Job Details Tab -->
                                        <div class="tab-pane" id="home" role="tabpanel"> <!-- style="margin-top: 54px;" -->

                                            <div class="form-body">
                                                <div class="row col-lg-6 col-md-6 lightGray-border pull-left" style="margin-right: 0px;border-top: 0px;border-bottom: 0px">

                                                    <div class="col-md-6">

                                                        <div id="total_cbm_div" style="display: block;">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <span><strong><?php echo $this->lang->line('total_cbm'); ?></strong></span>
                                                                    <input type="number" class="form-control inpstyl" id="total_cbm" min="0" step=0.01 name="total_cbm" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('company'); ?><span class="requiredField">*</span></label>
                                                            <select required class="form-control" name="company_id" id="company_id">
                                                                <!-- <option value="">Select Company</option> -->
                                                                <?php foreach ($companies as $value) { ?>
                                                                    <option value="<?php echo $value->id; ?>"><?php echo $value->company_name; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('job_status'); ?></label>
                                                            <select name="job_status" id="job_status" class="form-control inpstyl phone-group"> <!-- chzn-select  -->
                                                                <option value="">----Select Status---</option>
                                                                <?php foreach ($jobSatus as $row) { ?>
                                                                    <option value="<?php echo $row->options; ?>"><?php echo $row->options; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>


                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('job_type'); ?></label>
                                                            <select id="job_type_val" name="job_type_val" class="form-control inpstyl phone-group">
                                                                <option value="">----Select Option---</option>
                                                                <?php foreach ($jobTypes as $row) { ?>
                                                                    <option <?php //if($row->options=='Pick Up'){ echo "selected"; } ?> value="<?php echo $row->options; ?>"><?php echo $row->options; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('quote_type'); ?></label>
                                                            <select id="quote_type" name="quote_type" class="form-control inpstyl phone-group">
                                                                <option value="">----Select Option---</option>
                                                                <?php foreach ($quoteTypes as $row) { ?>
                                                                    <option value="<?php echo $row->options; ?>"><?php echo $row->options; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6" style="display: none;" id="house_size-div">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('house_size'); ?></label>
                                                            <select required id="house_size" name="house_size" class="form-control inpstyl phone-group">
                                                                <!-- <option value="">----Select Option---</option> -->
                                                                <?php foreach ($houseSizes as $row) { ?>
                                                                    <option value="<?php echo $row->house_type; ?>"><?php echo $row->house_type; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label><?php echo 'Special Item';?></label>
                                                            <textarea id="special_item_notes" name="special_item_notes" cols="58" rows="3"></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label><?php echo 'Quote List of Items';?></label>
                                                            <textarea id="quick_quote_list_of_items" name="quick_quote_list_of_items" cols="58" rows="3"></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <span><label><?php echo 'M3 Dimensions';?></label></strong></span>
                                                            <input type="text" class="form-control inpstyl" id="m3_dimensions" name="m3_dimensions" />
                                                        </div>
                                                    </div>



                                                    <!-- <div class="col-md-12 p-0">
                                                      <h4 style="padding-top: 10px;" class="text-primary"><?php //echo $this->lang->line('move_type');   ?></h4>
                                                      <hr/>
                                                      <div class="form-group">
                                                        <select id="move_type" name="move_type" class="form-control inpstyl phone-group">
                                                          <option value="">---Select Option---</option>
                                                    <?php //foreach ($moveTypes as $row) {   ?>
                                                            <option value="<?php //echo $row->id;   ?>"><?php //echo $row->options;   ?></option>
                                                    <?php //}   ?>
                                                        </select>
                                                      </div>

                                                      <div class="form-group" id="job_locationDiv" style="display: none;">
                                                        <label><?php //echo $this->lang->line('job_location');   ?></label>
                                                        <input type="text" class="form-control" placeholder="<?php //echo $this->lang->line('job_location');   ?>" id="job_location" name="job_location" />
                                                      </div>
                                                    </div> -->

                                                    <!-- When Details -->
                                                    <div class="col-md-12 pull-left" style="border-bottom: 0px;">
                                                        <h4 class="text-primary" style="padding-top: 10px;"><img style="height: 25px;width: 25px;border: 0;" src="<?php echo base_url(); ?>/assets/system_design/images/when-icon.png"><?php echo $this->lang->line('when'); ?>
                                                        </h4>
                                                        <hr/>
                                                        <div class="form-body">
                                                            <div class="row col-lg-12 col-md-12 pull-left" style="margin-right:0px;">

                                                                <div class="col-md-9" id="past_date_error" style="display: none;color: red;">
                                                                    <span>The Job Date cannot be in the past</span>
                                                                </div>

                                                                <div class="col-md-7">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('what_date_would_you_like_to_move'); ?>?<!-- <span class="requiredField">*</span> --></span>
                                                                        <input type="date" name="job_date" id="job_date" class="form-control inpstyl user-success">   <!-- min="2018-12-20" -->
                                                                    </div>
                                                                </div>

                                                                <!-- <div class="col-md-5" id="latest_pickup_dateDiv" style="display: none;">
                                                                  <div class="form-group">
                                                                    <span><?php echo $this->lang->line('backup_date'); ?>?</span>
                                                                    <input type="date" name="latest_pickup_date" id="latest_pickup_date" class="form-control inpstyl user-success">
                                                                  </div>
                                                                </div> -->

                                                                <div class="col-md-7">
                                                                    <!--  <div class="form-group" style="margin-bottom:10px;" id="timepicker1-div">
                                                                          <label><?php echo $this->lang->line('tentative_arrival_from'); ?></label>
                                                                          <input type="text" id="timepicker1" class="form-control" name="tentative_arrival_from" />
                                                                     </div> -->

                                                                    <!-- <div class="form-group" id="timepicker1-viewOnly" style="display: none;">
                                                                         <label><?php echo $this->lang->line('tentative_arrival_from'); ?></label>
                                                                         <input type="text" class="form-control" disabled id="viewStartTime">
                                                                    </div> -->

                                                                </div>

                                                                <div class="col-md-5">

                                                                </div>
                                                                <!-- <div class="col-md-12" style="margin-top: 15px;">
                                                                  <h4><?php //echo $this->lang->line('other_instructions');   ?></h4>
                                                                  <span id="other_instructions" class="instructions" style="display:none;"></span>
                                                                  <textarea class="ckeditor" id="editor5" cols="67" rows="5" name="other_instructions" class="instructions"></textarea>
                                                                </div> -->
                                                                <input type="hidden" name="other_instructions" id="other_instructions_val">
                                                                <input type="hidden" name="pickup_instructions" id="pickup_instructions_val">
                                                                <input type="hidden" name="delivery_instructions" id="delivery_instructions_val">
                                                                <input type="hidden" name="payment_instructions" id="payment_instructions_val">
                                                                <input type="hidden" name="insurance_instructions" id="insurance_instructions_val">
                                                                <input type="hidden" name="disclaimer_instructions" id="disclaimer_instructions_val">

                                                                <input type="hidden" name="page_type" id="page_type" value="<?php echo $type; ?>">

                                                            </div>
                                                        </div>
                                                    </div><!-- End of When Details -->


                                                </div>


                                                <div class="row col-lg-6 col-md-6 lightGray-border pull-left" style="margin-right:0px;border-bottom: 0px;border-left:0px;">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('first_name'); ?><span class="requiredField">*</span></label>
                                                            <input type="text" required id="cust_first_name" name="cust_first_name" class="typeahead form-control inpstyl" >
                                                            <!-- <ul class="dropdown-menu txtcustomer" style="top:38%;margin-left:15px;margin-right:0px;" role="menu" aria-labelledby="dropdownMenu"  id="DropdownCustomer">
                                                            </ul>   -->
                                                            <input type="hidden" id="customer_id" name="customer_id">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('last_name'); ?></label>
                                                            <input type="text" id="cust_last_name" name="cust_last_name" class="typeahead form-control inpstyl">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('phone'); ?><span class="requiredField">*</span></label>
                                                            <input type="text" required id="phone" name="phone" class="typeahead form-control inpstyl" >
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('email'); ?><span class="requiredField">*</span></label>
                                                            <input type="email" required id="email" name="email" class="typeahead form-control inpstyl">
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label><span><input name="spl_pet_transport" id="spl_pet_transport" class="checkbox-style" value="Y" type="checkbox" ></span>Pet Transportation</label>
                                                            <input type="text" id="spl_pet_transport_notes" name="spl_pet_transport_notes" class="form-control inpstyl" >
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label><span><input name="spl_veh_relocation" id="spl_veh_relocation" class="checkbox-style" value="Y" type="checkbox" ></span>Vehicle relocation</label>
                                                            <input type="text" id="spl_veh_relocation_notes" name="spl_veh_relocation_notes" class="form-control inpstyl" >
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label><span><input name="spl_piano_anitque" id="spl_piano_anitque" class="checkbox-style" value="Y" type="checkbox" ></span>Piano, antique furniture or other special items</label>
                                                            <input type="text" id="spl_piano_antique_notes" name="spl_piano_antique_notes" class="form-control inpstyl">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group" style="    margin-bottom: 2px;">
                                                            <label><span><input name="spl_prof_packing" id="spl_prof_packing" value="Y" class="checkbox-style" type="checkbox" ></span>Professional packing services</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group" style="    margin-bottom: 2px;">
                                                            <label><span><input name="spl_moving_boxes" id="spl_moving_boxes" value="Y" class="checkbox-style" type="checkbox" ></span>Moving boxes</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label><span><input name="spl_other_special" id="spl_other_special" class="checkbox-style" value="Y" type="checkbox" ></span>Other special requirements</label>
                                                            <input type="text" id="spl_other_special_notes" name="spl_other_special_notes" class="form-control inpstyl">
                                                        </div>
                                                    </div>

                                                </div>


                                                <!-- Pickup Dropoff Details -->
                                                <div class="col-md-8 lightGray-border pull-left" style="border-bottom: 0px;">

                                                    <!-- Pickup Details -->
                                                    <div class="col-md-6 left">
                                                        <h4 class="text-primary" style="padding-top: 10px;"><img style="height: 25px;width: 25px;border: 0;" src="<?php echo base_url(); ?>/assets/system_design/images/address-icon.png"><?php echo $this->lang->line('pick_up_details'); ?>
                                                        </h4>
                                                        <hr/>

                                                        <div class="form-body">
                                                            <div class="row col-lg-12 col-md-12 pull-left" style="margin-right:0px;">

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_first_name'); ?></span>
                                                                        <input type="text" id="pickup_first_name" name="pickup_first_name" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_last_name'); ?></span>
                                                                        <input type="text" id="pickup_last_name" name="pickup_last_name" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_phone'); ?></span>
                                                                        <input type="text" id="pickup_phone" name="pickup_phone" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_alt_phone'); ?></span>
                                                                        <input type="text" id="pickup_alt_phone" name="pickup_alt_phone" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_email'); ?></span>
                                                                        <input type="email" id="pickup_email" name="pickup_email" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_address1'); ?></span>
                                                                        <input type="text" name="pickup_address1" id="pickup_address1" autocomplete="on" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_address2'); ?></span>
                                                                        <input type="text" name="pickup_address2" id="pickup_address2" autocomplete="on" class="form-control inpstyl" >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_suburb'); ?><span class="requiredField">*</span></span>
                                                                        <input type="text" required name="pickup_suburb" id="pickup_suburb" autocomplete="on" class="form-control inpstyl" style="width: 88%;">
                                                                        <span class="pull-right"><a target="_blank" href="<?php echo 'https://www.google.com/maps/place/'.$job_data->pickup_suburb; ?>"><img src="<?php echo base_url(); ?>/assets/system_design/images/googlemaps_icon.png" style="width: 25px;margin-top: 5px;"></a></span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_floor'); ?></span>
                                                                        <select name="pickup_floor" id="pickup_floor" class="form-control inpstyl">
                                                                            <option value="0">----Select---</option>
                                                                            <?php for ($pfloor = 0; $pfloor <= 50; $pfloor++) { ?>
                                                                                <option value="<?php echo $pfloor; ?>"><?php
                                                                                    if ($pfloor == '0') {
                                                                                        echo "Ground";
                                                                                    } else {
                                                                                        echo $pfloor;
                                                                                    }
                                                                                    ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_lift'); ?></span>
                                                                        <select name="pickup_lift" id="pickup_lift" class="form-control inpstyl">
                                                                            <option value="No"></option>
                                                                            <option value="Yes">Yes</option>
                                                                            <option value="No">No</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php echo $this->lang->line('pickup_region'); ?></span>

                                                                        <select name="pickup_region" id="pickup_region" class="form-control inpstyl">
                                                                            <option value="">----Select---</option>
                                                                            <?php foreach ($pricingRegions as $reg) { ?>
                                                                                <option value="<?php echo $reg->region_id; ?>"><?php echo $reg->region_name; ?></option>
                                                                            <?php } ?>
                                                                        </select>

                                                                        <!-- <input type="text" name="pickup_region" id="pickup_region" class="form-control inpstyl" placeholder="<?php echo $this->lang->line('pickup_region'); ?>"> -->
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <span><?php
                                                                            echo 'Access Restrictions';
                                                                            //$this->lang->line('access_or_parking_restrictions');
                                                                            ?></span>
                                                                        <textarea id="pickup_access_restrictions"  name="pickup_access_restrictions" cols="31" rows="3"></textarea>
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Pickup Details -->

                                                    <!-- Dropoff Details -->
                                                    <div class="col-md-6 pull-left">
                                                        <h4 class="text-primary" style="padding-top: 10px;"><img style="height: 25px;width: 25px;border: 0;" src="<?php echo base_url(); ?>/assets/system_design/images/address-icon.png"><?php echo $this->lang->line('dropoff_details'); ?>
                                                        </h4>
                                                        <hr/>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_first_name'); ?></span>
                                                                <input type="text" id="delivery_first_name" name="delivery_first_name" class="form-control inpstyl">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_last_name'); ?></span>
                                                                <input type="text" id="delivery_last_name" name="delivery_last_name" class="form-control inpstyl">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_phone'); ?></span>
                                                                <input type="text" id="delivery_phone" name="delivery_phone" class="form-control inpstyl" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_alt_phone'); ?></span>
                                                                <input type="text" id="delivery_alt_phone" name="delivery_alt_phone" class="form-control inpstyl" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_email'); ?></span>
                                                                <input type="email" id="delivery_email" name="delivery_email" class="form-control inpstyl" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_address1'); ?></span>
                                                                <input type="text" name="delivery_address1" id="delivery_address1" autocomplete="on" class="form-control inpstyl" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_address2'); ?></span>
                                                                <input type="text" name="delivery_address2" id="delivery_address2" autocomplete="on" class="form-control inpstyl" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_suburb'); ?><span class="requiredField">*</span></span>
                                                                <input type="text" required name="delivery_suburb" id="delivery_suburb" autocomplete="on" class="form-control inpstyl" style="width:88%;" >
                                                                <span class="pull-right"><a target="_blank" href="<?php echo 'https://www.google.com/maps/place/'.$job_data->delivery_suburb; ?>"><img src="<?php echo base_url(); ?>/assets/system_design/images/googlemaps_icon.png" style="width: 25px;margin-top: 5px;"></a></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_floor'); ?></span>
                                                                <select name="delivery_floor" id="delivery_floor" class="form-control inpstyl">
                                                                    <option value="0">----Select---</option>
                                                                    <?php for ($dfloor = 0; $dfloor <= 50; $dfloor++) { ?>
                                                                        <option value="<?php echo $dfloor; ?>"><?php
                                                                            if ($dfloor == '0') {
                                                                                echo "Ground";
                                                                            } else {
                                                                                echo $dfloor;
                                                                            }
                                                                            ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_lift'); ?></span>
                                                                <select name="delivery_lift" id="delivery_lift" class="form-control inpstyl">
                                                                    <option value="No"></option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php echo $this->lang->line('delivery_region'); ?></span>

                                                                <select name="delivery_region" id="delivery_region" class="form-control inpstyl">
                                                                    <option value="">----Select---</option>
                                                                    <?php foreach ($pricingRegions as $reg) { ?>
                                                                        <option value="<?php echo $reg->region_id; ?>"><?php echo $reg->region_name; ?></option>
                                                                    <?php } ?>
                                                                </select>

                                                                <!-- <input type="text" name="delivery_region" id="delivery_region" class="form-control inpstyl" placeholder="<?php //echo $this->lang->line('delivery_region');   ?>"> -->
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <span><?php
                                                                    echo 'Access Restrictions';
                                                                    //$this->lang->line('access_or_parking_restrictions');
                                                                    ?></span>
                                                                <textarea id="delivery_access_restrictions" name="delivery_access_restrictions" cols="33" rows="3"></textarea>
                                                            </div>
                                                        </div>






                                                    </div>
                                                    <!-- End of Dropoff Details -->

                                                </div>
                                                <!-- End of Pickup Dropoff Details Details -->

                                                <!-- Map Area -->
                                                <div class="col-md-4 lightGray-border pull-left" style="border-left: 0px;border-bottom: 0px;">

                                                    <?php if($type=='edit'){ ?>
                                                        <button type="button" class="btn waves-effect waves-light btn-outline-danger" id="map-reload" />Reload</button>
                                                    <?php } ?>

                                                    <h4 class="text-primary" style="padding-top: 10px;"><img style="height: 25px;width: 25px;border: 0;" src="<?php echo base_url(); ?>/assets/system_design/images/address-icon.png">Map</h4>
                                                    <hr/>

                                                    <div class="col-md-12" style="padding: 0px;">
                                                        <div id="map"></div>
                                                    </div>
                                                    <div class="col-md-12" style="padding-right: 0px;padding-left: 6px;">
                                                        <div>Distance: <span id="totalDistance"></span></div>
                                                        <div>Time: <span id="totalDuration"></span></div>
                                                    </div>

                                                    <div class="form-group" style="margin-top: 20px;">
                                                        <label><?php echo $this->lang->line('city'); ?><span class="requiredField">*</span></label>
                                                        <select required class="form-control" name="city_id" id="city_id">
                                                            <?php foreach ($cities as $value) { ?>
                                                                <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                </div>
                                                <!-- End of Map Area -->

                                                <!-- Payment Details -->
                                                <div class="col-md-12 lightGray-border pull-left">
                                                    <h4 class="text-primary" style="padding-top: 10px;"><?php echo $this->lang->line('payment'); ?>
                                                    </h4>
                                                    <hr/>
                                                    <div class="col-md-6 pull-left lightGray-border" style="padding-top: 6px;border-bottom: 0px;">
                                                        <div class="col-md-6 pull-left">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label><?php echo $this->lang->line('price_structure'); ?></label>
                                                                    <select name="price_structure" id="price_structure" class="form-control inpstyl phone-group">
                                                                        <option value="">----Select Status---</option>
                                                                        <?php foreach ($priceStructure as $row) { ?>
                                                                            <option <?php
                                                                            if ($row->id == '74') {
                                                                                echo "selected";
                                                                            }
                                                                            ?> value="<?php echo $row->id; ?>"><?php echo $row->options; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>

                                                                <div class="form-group">  <!-- style="margin-bottom:10px;" -->
                                                                    <label><?php echo $this->lang->line('deposit_required'); ?></label>
                                                                    <input type="number" class="form-control inpstyl" id="deposit_agreed" step=0.01 min="0" name="deposit_agreed" />
                                                                </div>
                                                                <!-- <div class="form-group">
                                                                   <label><?php echo $this->lang->line('hourly_rate'); ?></label>
                                                                   <input type="number" class="form-control inpstyl" placeholder="Hourly Rate" id="hourly_rate" step=0.01 min="0" disabled name="hourly_rate" />
                                                                </div> -->

                                                                <!-- <div class="form-group">
                                                                     <label><?php echo $this->lang->line('call_out_fee'); ?></label>
                                                                     <input type="number" class="form-control inpstyl" placeholder="Call Out Fee" id="call_out_fee" step=0.01 min="0" name="call_out_fee" />
                                                                </div> -->

                                                            </div> <!-- End of col-md-12 -->
                                                        </div>

                                                        <div class="col-md-6 pull-left">
                                                            <div class="col-md-12">

                                                                <div class="form-group">
                                                                    <label><?php echo $this->lang->line('fixed_other_rate'); ?></label>
                                                                    <input type="number" class="form-control inpstyl" id="fixed_other_rate" step="0.01"  min="0" name="fixed_other_rate"  />
                                                                </div>

                                                                <!--  <div class="form-group">
                                                                    <label><?php echo $this->lang->line('minimum_hours'); ?></label>
                                                                    <input type="number" class="form-control inpstyl" placeholder="<?php echo $this->lang->line('minimum_hours'); ?>" id="minimum_hours" min="0" value="2" name="minimum_hours" disabled />
                                                                 </div> -->

                                                                <!-- <div class="form-group">
                                                                     <label><?php echo $this->lang->line('fuel_charges'); ?></label>
                                                                     <input type="number" class="form-control inpstyl" placeholder="<?php echo $this->lang->line('fuel_charges'); ?>" id="fuel_charges" step="0.01"
                                                                     min="0" name="fuel_charges" />
                                                                </div> -->

                                                            </div>

                                                        </div>

                                                        <div class="col-md-12 pull-left">
                                                            <div class="form-group" style="margin-bottom: 17px;">
                                                                <label><?php echo $this->lang->line('payment_notes'); ?></label>
                                                                <textarea id="payment_notes" name="payment_notes" cols="31" rows="4" class="form-control user-success"></textarea>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6 pull-left lightGray-border" style="padding-top: 6px;border-bottom: 0px;border-left: 0px;">
                                                        <div class="col-md-6 pull-left">
                                                            <div class="col-md-12">

                                                                <div class="form-group">
                                                                    <label><?php echo $this->lang->line('payment_status'); ?></label>
                                                                    <select disabled name="payment_status" id="payment_status" class="form-control inpstyl phone-group">
                                                                        <option value="">----Select Option---</option>
                                                                        <?php foreach ($paymentStatus as $row) { ?>
                                                                            <option value="<?php echo $row->options; ?>"><?php echo $row->options; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <!-- <div class="form-group" style="margin-bottom:10px;">
                                                                   <label><?php //echo $this->lang->line('deposit_agreed');  ?></label>
                                                                   <input type="number" class="form-control inpstyl" placeholder="<?php //echo $this->lang->line('deposit_agreed');   ?>" id="deposit_agreed" step=0.01 min="0" name="deposit_agreed" />
                                                                </div> -->

                                                                <!-- <div class="form-group">
                                                                    <label><?php echo $this->lang->line('deposit_paid'); ?></label>
                                                                    <input type="number" class="form-control inpstyl" placeholder="Deposit Amount" id="deposit_amt" step=0.01 min="0" name="deposit_amt" disabled />
                                                                </div> -->

                                                                <!-- <div class="form-group">
                                                                    <label><?php echo $this->lang->line('payment_type'); ?></label>
                                                                    <select disabled name="payment_type" id="payment_type" class="form-control inpstyl phone-group">
                                                                        <option value="">----Select Option---</option>
                                                                        <?php foreach ($paymentTypes as $row) { ?>
                                                                            <option value="<?php echo $row->id; ?>"><?php echo $row->options; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div> -->

                                                                <div class="col-md-12 depositDetails" style="padding:0px;">

                                                                    <div class="form-group">
                                                                        <label><?php echo $this->lang->line('total_hours'); ?></label>
                                                                        <input type="number" class="form-control inpstyl" id="total_hours" min="0" step=0.01 name="total_hours" />
                                                                    </div>

                                                                </div> <!-- End of depositDetails -->

                                                            </div>

                                                            <!--  <div class="col-md-12 otherChargesDetails">

                                                               <div class="form-group" style="margin-bottom: 15px;">
                                                                <label><?php //echo $this->lang->line('other_charges_type');   ?></label>
                                                                <select name="other_charges_type" id="other_charges_type" class="form-control inpstyl phone-group">
                                                                 <option value="">----Select Option---</option>
                                                            <?php //foreach ($otherChargesType as $row) {   ?>
                                                                 <option value="<?php //echo $row->options;    ?>"><?php //echo $row->options;    ?></option>
                                                            <?php //}   ?>
                                                               </select>
                                                               </div>

                                                             </div> -->

                                                            <div class="col-md-12 depositDetails">
                                                                <div class="form-group">
                                                                    <label><?php echo $this->lang->line('total_amount'); ?></label>
                                                                    <input type="number" readonly class="form-control inpstyl" id="total_amount" step=0.01 min="0" name="total_amount" />
                                                                </div>

                                                            </div> <!-- End of depositDetails -->


                                                        </div>

                                                        <div class="col-md-6 pull-left">

                                                            <div class="col-md-12">
                                                                <!-- <div class="form-group">
                                                                    <label><?php echo $this->lang->line('payment_reference'); ?></label>
                                                                    <input disabled type="text" class="form-control inpstyl" placeholder="Payment Reference" id="payment_reference" name="payment_reference" />
                                                                </div> -->


                                                                <div class="col-md-12 depositDetails" style="padding:0px;">
                                                                    <!-- margin-top: -26px;<div class="form-group" id="actualHoursDiv" style="padding:0px;">
                                                                       <input type="checkbox" style="float: left;height: 34px;width: 7%;" id="use_actual_hours" value="yes" name="use_actual_hours" />
                                                                       <span style="float: left;margin-top: 6px;margin-left: 10px;"><?php //echo $this->lang->line('use_actual_hours');   ?></span>
                                                                    </div> -->
                                                                    <div class="form-group">
                                                                        <label><?php echo $this->lang->line('actual_hours'); ?></label>
                                                                        <input type="number" readonly class="form-control inpstyl"  id="actual_hours" min="0" name="actual_hours" />
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label><?php echo $this->lang->line('how_did_you_hear_about_us'); ?></label>
                                                                    <select name="lead_info" id="lead_info" class="form-control inpstyl phone-group">
                                                                        <option value="">----Select Option---</option>
                                                                        <?php foreach ($hearAbtUs as $row) { ?>
                                                                            <option value="<?php echo $row->id; ?>"><?php echo $row->options; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>



                                                                <!-- <div class="col-md-12 otherChargesDetails" style="padding:0px;">

                                                                  <div class="form-group">
                                                                   <label><?php //echo $this->lang->line('other_charges');   ?></label>
                                                                   <input type="number" class="form-control inpstyl" placeholder="<?php //echo $this->lang->line('other_charges');   ?>" id="other_charges_amt" step=0.01 min="0" name="other_charges_amt" />
                                                                  </div>
                                                                </div> -->

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--  <div class="col-md-12 pull-left" style="margin-top: 10px;">
                                                       <div class="form-group">
                                                         <h4><?php //echo $this->lang->line('payment_instructions');   ?></h4>
                                                         <span id="payment_instructions" class="instructions" style="display:none;"></span>
                                                         <textarea class="ckeditor" id="editor6" cols="126" rows="6" class="instructions" name="payment_instructions" id="payment_instructions_val" placeholder="<?php //echo $this->lang->line('payment_instructions');   ?>"></textarea>
                                                       </div>
                                                     </div> -->
                                                </div>
                                                <!-- End of Payment Details -->

                                                <!-- Other Details -->
                                                <div class="col-md-6 lightGray-border pull-left" style="border-top: 0px;">
                                                    <h4 class="text-primary" style="padding-top: 18px;"><?php echo $this->lang->line('other_details'); ?>
                                                    </h4>
                                                    <hr/>
                                                    <div class="form-group" style="margin-bottom: 5px;">
                                                        <?php echo $this->lang->line('anyother_important_details'); ?>
                                                        <textarea id="notes" name="notes" class="form-control" cols="59" rows="11"></textarea>
                                                    </div>

                                                </div>
                                                <!-- End of Other Details -->


                                                <!-- Vehicle Details -->
                                                <div class="col-md-6 lightGray-border pull-left" style="border-left: 0px;border-top: 0px;">
                                                    <h4 class="text-primary" style="padding-top: 10px;"><img style="height: 30px;width: 30px;border: 0;" src="<?php echo base_url(); ?>/assets/system_design/images/truck-icon.png"><?php echo $this->lang->line('vehicle'); ?>
                                                    </h4>
                                                    <hr/>

                                                    <!-- <div class="col-md-12">

                                                      <div class="form-group">
                                                          <label><?php echo $this->lang->line('size_of_truck'); ?><span class="requiredField">*</span></label>
                                                          <select required name="size_of_truck" id="size_of_truck" class="form-control">
                                                            <option value="">----Select Option---</option>
                                                    <?php foreach ($truckSizes as $row) { ?>
                                                                        <option value="<?php echo $row->id; ?>"><?php echo $row->options; ?></option>
                                                    <?php } ?>
                                                          </select>
                                                      </div>

                                                      <div class="form-group">
                                                           <label><?php echo $this->lang->line('no_of_men'); ?></label>
                                                           <input type="number" class="form-control inpstyl" placeholder="No. of Men" id="no_of_men" min="0" name="no_of_men" />
                                                      </div>

                                                  </div> -->

                                                    <div class="col-md-12">

                                                        <div class="form-group">
                                                            <label><?php echo $this->lang->line('vehicle'); ?></label>
                                                            <select name="vehicle_id" id="job_vehicle_id" class="form-control">
                                                                <option value="">Select Subcontractor</option>
                                                                <?php
                                                                $city = '';
                                                                foreach ($vehicles as $value) {
                                                                    /*if ($city != $value->city_name) {
                                                                        if ($city != '') {
                                                                            echo '</optgroup>';
                                                                        }
                                                                        echo '<optgroup label="' . ucfirst($value->city_name) . '">';
                                                                    }*/
                                                                    echo '<option value="' . $value->id . '">' . htmlspecialchars($value->name) . '</option>';
                                                                    $city = $value->city_name;
                                                                }
                                                                /*if ($city != '') {
                                                                    echo '</optgroup>';
                                                                }*/
                                                                ?>
                                                            </select>
                                                        </div>




                                                        <!-- <div class="form-group">
                                                           <label class="control-label"><?php //echo $this->lang->line('description');   ?></label>
                                                        <?php //echo form_input($description);  ?>
                                                        </div> -->
                                                    </div>
                                                </div>
                                                <!-- End Vehicle Details -->

                                                <div class="col-md-12 pull-left" style="margin-bottom: 10px;margin-top: 10px;">
                                                    <div class="form-body">
                                                        <div class="row">
                                                            <table>
                                                                <tr><td></td><td></td><td></td></tr>
                                                                <tr>
                                                                    <td><strong>Flagfall</strong></td>
                                                                    <td></td>
                                                                    <td>$ <input type="text" name="price_flat" id="price_flat" value="<?php echo $pricing_table_details['price_flat']; ?>"></td>
                                                                </tr>

                                                                <tr>
                                                                    <td><input type="hidden" name="calc_job_cbm" id="calc_job_cbm" value="<?php echo $pricing_table_details['calc_job_cbm']; ?>"><?php echo $pricing_table_details['calc_job_cbm']; ?> CBM</td>
                                                                    <td>$<input type="text" name="price_per_cbm" id="price_per_cbm" value="<?php echo $pricing_table_details['price_per_cbm']; ?>"> /CBM</td>
                                                                    <td>$<span id="line_total"><?php echo $pricing_table_details['line_total'];  ?></span>
                                                                        <input type="hidden" name="line_total_val" id="line_total_val" value="<?php echo $pricing_table_details['line_total']; ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr><td></td><td></td><td></td></tr>

                                                                <tr>
                                                                    <td><strong>Excess KM</strong></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>

                                                                <tr>
                                                                    <td><?php echo $pricing_table_details['pickup_region_name']; ?> <input type="text" name="pickup_excess_km" id="pickup_excess_km" value="<?php echo $pricing_table_details['pickup_excess_km']; ?>"></td>

                                                                    <td>$<input type="text" name="pickup_price_excess_km" id="pickup_price_excess_km" value="<?php echo $pricing_table_details['pickup_price_excess_km']; ?>"> /km</td>

                                                                    <td>$<span id="pickup_excess_charges"><?php echo $pricing_table_details['pickup_excess_charges']; ?></span>
                                                                        <input type="hidden" name="pickup_excess_charges_val" id="pickup_excess_charges_val" value="<?php echo $pricing_table_details['pickup_excess_charges']; ?>">
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td><?php echo $pricing_table_details['delivery_region_name']; ?> <input type="text" name="delivery_excess_km" id="delivery_excess_km" value="<?php echo $pricing_table_details['delivery_excess_km']; ?>"></td>

                                                                    <td>$<input type="text" name="delivery_price_excess_km" id="delivery_price_excess_km" value="<?php echo $pricing_table_details['delivery_price_excess_km']; ?>"> /km</td>

                                                                    <td>$<span id="delivery_excess_charges"><?php echo $pricing_table_details['delivery_excess_charges']; ?></span>
                                                                        <input type="hidden" name="delivery_excess_charges_val" id="delivery_excess_charges_val" value="<?php echo $pricing_table_details['delivery_excess_charges']; ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr><td></td><td></td><td></td></tr>

                                                                <tr>
                                                                    <td><strong>Lift Surcharges</strong></td><td></td><td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Pickup</td>
                                                                    <td>$<input type="text" name="pickup_access_cbm_lev" readonly id="pickup_access_cbm_lev" value="<?php echo $pricing_table_details['pickup_access_cbm_lev']; ?>"> /CBM</td>
                                                                    <td>$<span id="pickup_lift_charges"><?php echo $pricing_table_details['pickup_lift_charges']; ?></span>
                                                                        <input type="hidden" id="pickup_lift_charges_val" name="pickup_lift_charges_val" value="<?php echo $pricing_table_details['pickup_lift_charges']; ?>"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Delivery</td>
                                                                    <td>$<input type="text" readonly name="delivery_access_cbm_lev" id="delivery_access_cbm_lev" value="<?php echo $pricing_table_details['delivery_access_cbm_lev']; ?>"> /CBM</td>

                                                                    <td>$<span id="delivery_lift_charges"><?php echo $pricing_table_details['delivery_lift_charges']; ?></span>
                                                                        <input type="hidden" id="delivery_lift_charges_val" name="delivery_lift_charges_val" value="<?php echo $pricing_table_details['delivery_lift_charges']; ?>"></td>
                                                                </tr>
                                                                <tr><td></td><td></td><td></td></tr>


                                                                <tr>
                                                                    <td>Pickup Suburb Surcharge</td>
                                                                    <td></td>
                                                                    <td>$<input type="text" name="pickup_suburb_surcharges" id="pickup_suburb_surcharges" value="<?php echo $pricing_table_details['pickup_suburb_surcharges']; ?>"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Delivery Suburb Surcharge</td>
                                                                    <td></td>
                                                                    <td>$<input type="text" name="delivery_suburb_surcharges" id="delivery_suburb_surcharges" value="<?php echo $pricing_table_details['delivery_suburb_surcharges']; ?>"></td>
                                                                </tr>
                                                                <tr><td></td><td></td><td></td></tr>

                                                                <tr>
                                                                    <td>Seasonal Surcharge</td><td> <?php echo $pricing_table_details['seasonal_surcharges'].'%'; ?>
                                                                        <input type="hidden" name="seasonal_surcharges" id="seasonal_surcharges" value="<?php echo $pricing_table_details['seasonal_surcharges'].'%'; ?>">
                                                                    </td><td></td>
                                                                </tr>


                                                                <tr>
                                                                    <td>Second Sector</td><td> $<input type="text" name="second_sector_rate" id="second_sector_rate" value="<?php echo $pricing_table_details['second_sector_rate']; ?>">
                                                                    </td><td>
                                                                        <span id="second_sector_total_span"><?php echo $pricing_table_details['second_sector_total']; ?></span>
                                                                        <input type="hidden" name="second_sector_total" id="second_sector_total" value="<?php echo $pricing_table_details['second_sector_total']; ?>">
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td>Misc Charges</td><td> $<input type="number" step="0.01" name="misc_charges" id="misc_charges" value="<?php echo $pricing_table_details['misc_charges']; ?>">
                                                                    </td><td></td>
                                                                </tr>

                                                                <tr><td></td><td></td><td></td></tr>

                                                                <tr>
                                                                    <td>Total</td>
                                                                    <td>$<?php echo $pricing_table_details['total']; ?>
                                                                        <input type="hidden" value="<?php echo $pricing_table_details['total']; ?>" name="pricing_table_total" id="pricing_table_total">
                                                                    </td>
                                                                    <td><span id="total_pricing"></span></td>
                                                                </tr>

                                                                <tr>
                                                                    <td></td>
                                                                    <td><button type="button" id="recalculate_pricing_btn" class="btn waves-effect waves-light btn-outline-danger">Recalculate</button></td>
                                                                    <td></td>
                                                                </tr>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-8 pull-left" style="border-top: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-actions">
                                                            <input type="hidden" value="" id="job_type" name="job_type">
                                                            <input type="hidden" value="1" name="create_job_btn">
                                                            <input type="hidden" name="save_as" id="save_as" value="job">
                                                            <input type="hidden" id="update_rec_id" name="update_rec_id">
                                                            <input type="hidden" id="calculator_data" name="calculator_data">
                                                            <input type="hidden" id="calculated_cbm" name="calculated_cbm">
                                                            <input type="hidden" id="redirect_path" name="redirect_path" value="<?php echo $page_name; ?>">
                                                            <input type="hidden" id="selected_tab" name="selected_tab" value="">
                                                            <input type="hidden" id="display_cbm" name="display_cbm" value="">
                                                            <!-- <button type="button" class="btn btn-info" style="float: right;margin-left: 10px;" data-dismiss="modal">Close</button> -->
                                                            <button type="submit" id="submit_job_btn" class="btn waves-effect waves-light btn-outline-danger submit_job_btn"> <i class="fa fa-check"></i> <?php echo $this->lang->line('make_a_booking'); ?></button>
                                                            <a class="btn waves-effect waves-light btn-outline-danger" href="<?php echo site_url(); ?>/admin/<?php echo $page_name; ?>"><i class="fa fa-arrow-left"></i>  Go Back</a>

                                                            <!-- <input type="submit" id="submit_job_btn" value="<?php echo $this->lang->line('make_a_booking'); ?>" name="submit" />   -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!--End of 1st form Body-->




                                            <!-- <div class="form-body">
                                                   <div class="row">

                                                   </div>
                                            </div> --><!--End of 2nd form Body-->

                                        </div>


                                        <div class="tab-pane p-20 pull-left" id="calculatorTab" role="tabpanel">
                                            <?php $this->load->view('admin/sales/space_calculator'); ?>
                                        </div>

                                        <div class="tab-pane p-20 pull-left" id="invoicingTab" role="tabpanel">
                                            <!-- <div style="width: 100%;float: left;" id="generateQuoteBtnDiv">

                                              <a style="float: left;margin-top: 17px;" id="generateQuotePdf" href="javascript:" class="btn btn-success">
                                                <span class="glyphicon glyphicon-flash"></span> Generate Quote
                                              </a>

                                              <a id="quoteEditHref" class="btn btn-danger" style="display:none;margin-left: 70px;" href="javascript:">Edit</a>

                                              <a style="display: none;margin-left: 62px;" id="quotePdfHref" href="#" target="_blank">
                                              <img style="width: 55px;cursor: pointer;margin-bottom: 12px;" src="<?php echo base_url(); ?>/assets/system_design/images/pdf-icon.png">
                                              </a>
                                            </div> -->

                                            <!-- <div style="width: 100%;float: left;" id="workOrderBtnDiv">
                                              <a style="float: left;margin-top: 17px;" id="downloadWoPdf" href="javascript:" class="btn btn-success">
                                                <span class="glyphicon glyphicon-flash"></span> Generate Work Order
                                              </a>
                                              <a id="woEditHref" style="display:none;margin-left: 122px;"></a>
                                              <a style="display: none;margin-left: 29px;" id="workOrderPdfLink" href="#" target="_blank">
                                              <img style="width: 55px;cursor: pointer;margin-bottom: 12px;" src="<?php echo base_url(); ?>/assets/system_design/images/pdf-icon.png">
                                              </a>
                                            </div> -->

                                            <div style="width: 100%;float: left;" id="invoiceBtnDiv">
                                                <a href="javascript:" id="generateInvoiceBtn" style="margin-top: 3px;" class="btn btn-success">
                                                    <span class="glyphicon glyphicon-flash"></span> Generate Invoice
                                                </a>

                                                <a id="invoiceHref" class="btn btn-danger" style="display:none;margin-left: 59px;margin-right: 7px;" href="#">Edit</a>

                                                <a target="blank" style="display: none;margin-left: 55px;" id="invoicePdfLink" href="#"><img style="width: 55px;cursor: pointer;" src="<?php echo base_url(); ?>/assets/system_design/images/invoice-icon.png"></a>
                                            </div>

                                            <div id="inventoryDiv" style="margin-top: 12px;">
                                                <a href="javascript:" id="generateInventoryBtn" style="float: left;margin-top: 21px;" class="btn btn-success">
                                                    <span class="glyphicon glyphicon-flash"></span> Generate Inventory List
                                                </a>
                                                <a id="inventoryEditHref" style="display:none;margin-left: 117px;"></a>
                                                <a style="display: none;margin-left: 20px;" id="inventoryHref" href="#" target="_blank"><img style="width: 55px;cursor: pointer;margin-top: 10px;" src="<?php echo base_url(); ?>/assets/system_design/images/inventory-icon.png"></a>
                                            </div>


                                            <div class="table-responsive pull-left" style="margin-top: 35px;">
                                                <table id="example1" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">

                                                    <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('item'); ?></th>
                                                        <th><?php echo $this->lang->line('description'); ?></th>
                                                        <th><?php echo $this->lang->line('quantity'); ?></th>
                                                        <th><?php echo $this->lang->line('unit_price'); ?></th>
                                                        <th><?php echo $this->lang->line('total'); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $i = 1;
                                                    foreach ($invoice_items as $row):
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $row->title; ?></td>
                                                            <td><?php echo $row->description; ?></td>
                                                            <td><?php echo $row->quantity; ?></td>
                                                            <td><?php echo $row->rate; ?></td>
                                                            <td>&#36;<?php echo $row->total; ?></td>

                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>

                                                <div class="clearfix">

                                                    <div class="pull-right" id="invoice-total-section">
                                                        <table id="invoice-item-table" class="table display dataTable text-right strong table-responsive">
                                                            <tbody>
                                                            <?php $invoiceAllPaymentDetails = getInvoiceAmountsDetails($invoice_detail->id);
                                                            ?>
                                                            <tr>
                                                                <td>Total Amount</td>
                                                                <td><?php
                                                                    echo '&#36;' . $invoiceAllPaymentDetails['invoice_total_amount'];
                                                                    ;
                                                                    ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Deposit Paid</td>
                                                                <td><?php echo '&#36;' . $invoiceAllPaymentDetails['invoice_deposit_paid']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Total Payment Received</td>
                                                                <td><?php echo ' &#36;' . $invoiceAllPaymentDetails['total_receieved_payment']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Balance Due</td>
                                                                <td><?php echo ' &#36;' . $invoiceAllPaymentDetails['invoice_balance_due']; ?></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="table-responsive pull-left" style="margin-top: 35px;">
                                                <h4>Payments Received</h4>
                                                <table id="example1" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">

                                                    <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('amount'); ?></th>
                                                        <th><?php echo $this->lang->line('payment_date'); ?></th>
                                                        <th><?php echo $this->lang->line('payment_method'); ?></th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <?php
                                                    $i = 1;
                                                    foreach ($invoice_payments as $row):
                                                        ?>
                                                        <tr>
                                                            <td>&#36;<?php echo $row->amount; ?></td>
                                                            <td><?php echo date('d-m-Y', strtotime($row->payment_date)); ?></td>
                                                            <td><?php echo $row->payment_method; ?></td>
                                                            <!-- <td><?php //echo $row->notes;   ?></td>       -->
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>


                                        </div>
                                        <!-- End of invoicingTab -->

                                        <!-- Start of Email Tab -->
                                        <div class="tab-pane p-20 pull-left" id="emailTab" role="tabpanel">
                                            <?php //echo "<pre>";print_r($job_data); ?>
                                            <div class="col-md-12">
                                                <span id="email_customer_name"></span>
                                                <span>

                                                    <div id="email-message" style="display: none;float: right;" class="alert">
                                                        <a href="#" class="close" data-dismiss="alert">×</a>
                                                    </div>
                                                </span>
                                            </div>

                                            <div class="col-md-12 pull-left">

                                                <div class="col-md-8 pull-left">

                                                    <div class="col-md-12 pull-left" style="margin-bottom: 5px;">
                                                        <div class="col-md-4 pull-left">
                                                            <input type="checkbox" style="float: left;height: 32px;width: 16px;" id="email_customer_checkbox" name="email_customer_checkbox" />
                                                            <span style="float: left;margin-top: 6px;margin-left: 5px;">Email Customer</span>

                                                            <!-- <select style="margin-top: 10px;" name="email_customer_type" id="email_customer_type" class="form-control" disabled>
                                                                <option value="">Select Email</option>
                                                                <option value="<?php echo $job_data->customer_email; ?>">Email</option>
                                                                <option value="<?php echo $job_data->pickup_email; ?>">Pickup Email</option>
                                                                <option value="<?php echo $job_data->delivery_email; ?>">Delivery Email</option>
                                                            </select> -->

                                                        </div>
                                                        <div class="col-md-4 pull-left">
                                                            <input type="checkbox" style="float: left;height: 32px;width: 16px;" id="email_driver_checkbox" name="email_driver_checkbox" />
                                                            <span style="float: left;margin-top: 6px;margin-left: 4px;">Email Subcontractor</span>
                                                        </div>

                                                        <div class="col-md-4 pull-left">
                                                            <select name="contractor_email_id" id="contractor_email_id" class="form-control" disabled>
                                                                <option value="">Select Subcontractor</option>
                                                                <?php
                                                                $city = '';
                                                                foreach ($vehicles as $value) {
                                                                    /* if ($city != $value->city_name) {
                                                                             if ($city != '') {
                                                                                  echo '</optgroup>';
                                                                             }
                                                                          echo '<optgroup label="' . ucfirst($value->city_name) . '">';
                                                                         }*/

                                                                    echo '<option value="' . $value->id . '">' . htmlspecialchars($value->name) . '</option>';
                                                                    $city = $value->city_name;
                                                                }
                                                                /*if ($city != '') {
                                                                    echo '</optgroup>';
                                                                }*/
                                                                ?>
                                                            </select>

                                                            <select style="margin-top: 10px;" name="email_contractor_type" id="email_contractor_type" class="form-control" disabled>
                                                                <option value="">Select Email</option>
                                                                <option value="">Operations Email</option>
                                                                <option value="">Admin Email</option>
                                                                <option value="">Quote Email</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 pull-left">
                                                        <span class="col-md-2">To:</span>
                                                        <span class="col-md-10"><input type="text" class="form-control" name="email_to" id="email_to"></span>
                                                    </div>
                                                    <div  class="col-md-12 pull-left">
                                                        <span class="col-md-2" style="margin-top: 6px;">Subject:</span>
                                                        <span class="col-md-10"><input class="form-control" type="text" name="email_subject" id="email_subject"></span>

                                                        <span class="col-md-2"  style="margin-top: 6px;">CC:</span>
                                                        <span class="col-md-10"><input type="text" class="form-control" name="email_cc" id="email_cc"></span>

                                                        <span class="col-md-2"  style="margin-top: 6px;">BCC:</span>
                                                        <span class="col-md-10"><input type="text" style="margin-bottom: 5px;" class="form-control" name="email_bcc" id="email_bcc"></span>
                                                    </div>

                                                    <div  class="col-md-12 pull-left" style="margin-left: 10px;">
                                                        <div class="form-group">
                                                            <textarea class="ckeditor" id="editor4" name="email_content" cols="100" rows="10"></textarea>
                                                            <script>
                                                                CKEDITOR.replace('email_content', {
                                                                    customConfig: '<?php echo base_url(); ?>/assets/system_design/ckeditor/config-attach.js',
                                                                    autoCloseUpload: true,
                                                                    //autoClose attachment container on attachment upload
                                                                    validateSize: 100, //100mb size limit
                                                                    filebrowserUploadUrl: '<?php echo site_url(); ?>/admin/upload_email_attachment',
                                                                    //'<?php echo base_url(); ?>/uploads/email_attachments/',
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 pull-left">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Email Templates</label>
                                                            <select name="email_template" id="email_template" class="form-control phone-group">  <!-- chzn-select -->
                                                                <option value="">----Select Option---</option>
                                                                <?php //foreach ($emailTemplates as $row) {  ?>
                                                                <!-- <option value="<?php echo $row->id; ?>"><?php //echo $row->template_name; ?></option> -->
                                                                <?php //}  ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">


                                                        <div class="pull-left" style="border: 0px solid red;width: 100%;">
                                                            <label class="pull-left" style="margin-top: 8px;">Attachments</label>
                                                            <span class="pull-right"><input style="width:17px;margin-right:4px;margin-left: 5px;" id="attachments_select_all" type="checkbox" class="form-control"></span>
                                                            <span class="pull-right" style="margin-top: 8px;">Select All</span>
                                                        </div>


                                                        <div id="attachments" style="border: 1px solid gray;overflow-y: scroll; height:381px;">
                                                            <?php
                                                            $directory = getcwd() . "/" . '/uploads/pdfs/workOrders/';
                                                            $contents = scandir($directory);
                                                            if ($contents) {
                                                                foreach ($contents as $key => $value) {
                                                                    if ($value == "." || $value == "..") {
                                                                        unset($contents[$key]);
                                                                    }
                                                                }
                                                            }
                                                            $src = base_url() . '/assets/system_design/images/pdf-icon.png';
                                                            echo "<ul id='attachmentsul' style='list-style: none;margin-left: 10px;padding: 5px;'>";
                                                            echo "</ul>";
                                                            ?>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-6 pull-left">
                                                    <div class="form-actions">
                                                        <span>
                                                            <a id="sendEmailBtn" href="javascript:" class="btn waves-effect waves-light btn-outline-danger">Send</a>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- End of Email Tab -->

                                        <!-- Start of Attachment Tab -->
                                        <div class="tab-pane p-20" id="attachmentTab" role="tabpanel">

                                            <div class="col-lg-12 col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <!-- <h4 class="card-title">File Upload1</h4>
                                                        <label for="input-file-now">Your so fresh input file — Default version</label> -->
                                                        <input type="file" id="file-input" class="dropify" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview-->
                                            <div id='preview'></div>

                                            <div class="col-md-12">
                                                <div id="progress-wrp" style="float: left;width: 100%;display: none;">
                                                    <div class="progress-bar" id="progress-bar"></div >
                                                    <div class="status" id="progress-status">0%</div>
                                                </div>
                                                <a id="upload" class="btn waves-effect waves-light btn-outline-danger"> <i class="fa fa-upload"></i> Upload</a>

                                                <!-- <input type='button' class="btn waves-effect waves-light btn-outline-danger" value='Upload' id='upload'> -->
                                            </div>


                                        </div>
                                        <!-- End of Attachment Tab -->

                                        <!-- Start of Job Log Tab -->
                                        <div class="tab-pane p-20 pull-left" id="JobLogTab" role="tabpanel">
                                            <div class="col-lg-12 col-md-12">

                                                <div class="col-md-6 pull-left">
                                                    <textarea style="float: left;border: 3px double #ccc;resize: none;height: 140px;" id="log_notes" name="log_notes" cols="50" rows="15" placeholder="Type a note here..."></textarea>
                                                    <span style="margin-top: 20px;float: left;margin-left: 5px;">
                                                        <a id="saveJobLogNotes" href="javascript:" class="btn waves-effect waves-light btn-outline-danger">Save Note</a>
                                                    </span>
                                                </div>

                                                <div class="col-md-6 pull-left">

                                                    <div class="col-md-6 pull-left">
                                                        <span class="pull-left"><input style="width:17px;margin-right:4px;" id="sms_customer_checkbox" type="checkbox" class="form-control"></span>
                                                        <span class="pull-left" style="margin-top: 8px;">SMS Customer</span>

                                                        <select style="margin-bottom: 10px;" name="sms_customer_type" id="sms_customer_type" class="form-control" disabled>
                                                            <option value="">Select Phone</option>
                                                            <option value="<?php echo $job_data->customer_phone; ?>">Phone</option>
                                                            <option value="<?php echo $job_data->pickup_phone; ?>">Pickup Phone</option>
                                                            <option value="<?php echo $job_data->pickup_alt_phone; ?>">Pickup Alternate Phone</option>
                                                            <option value="<?php echo $job_data->delivery_phone; ?>">Dropoff Phone</option>
                                                            <option value="<?php echo $job_data->delivery_alt_phone; ?>">Dropoff Alternate Phone</option>
                                                        </select>

                                                    </div>

                                                    <div class="col-md-6 pull-left">
                                                        <span class="pull-left"><input style="width:17px;margin-right:4px;" id="sms_subcontractor_checkbox" type="checkbox" name="" class="form-control"></span>
                                                        <span class="pull-left" style="margin-top: 8px;">SMS Subcontractor</span>

                                                        <select style="margin-bottom: 10px;" name="contractor_sms_id" id="contractor_sms_id" class="form-control" disabled>
                                                            <option value="">Select Subcontractor</option>
                                                            <?php
                                                            $city = '';
                                                            foreach ($vehicles as $value) {
                                                                echo '<option value="' . $value->id . '">' . htmlspecialchars($value->name) . '</option>';
                                                                $city = $value->city_name;
                                                            }
                                                            ?>
                                                        </select>

                                                        <select style="margin-bottom: 10px;" name="sms_subcontractor_number" id="sms_subcontractor_number" class="form-control" disabled>
                                                            <option value="">Select Phone</option>
                                                            <option value="">Phone 1</option>
                                                            <option value="">Phone 2</option>
                                                            <option value="">Phone 3</option>
                                                            <option value="">Phone 4</option>
                                                        </select>

                                                    </div>

                                                    <select id="sms_template_id" style="margin-bottom: 10px;" class="form-control">
                                                        <option value="">Select SMS Template</option>
                                                        <?php foreach ($sms_templates as $smsTemp) { ?>
                                                            <option value="<?php echo $smsTemp->message; ?>"><?php echo $smsTemp->template_name; ?></option>
                                                        <?php } ?>

                                                    </select>

                                                    <input type="text" name="sms_number" id="sms_number" class="form-control" placeholder="SMS Number" style="border: 3px double #ccc;">
                                                    <textarea style="float: left;border: 3px double #ccc;resize: none;margin-top: 4px;height: 96px;" id="customer_message" name="customer_message" cols="56" rows="15" placeholder="Type your sms message here..."></textarea>
                                                    <span style="margin-top: 20px;float: left;margin-left: 5px;">
                                                        <a id="send_message" href="javascript:" class="btn waves-effect waves-light btn-outline-danger"><?php echo $this->lang->line('send'); ?></a>
                                                    </span>
                                                </div>

                                                <div id="jobLog-message" style="display: none;float:left;margin-left:40px;margin-top:5px;width: 80%;" class="alert">
                                                    <a href="#" class="close" data-dismiss="alert">×</a>
                                                </div>

                                            </div>

                                        </div>
                                        <!-- End of Job Log Tab -->


                                        <!-- Start of Email Log Tab -->
                                        <div class="tab-pane pull-left" id="EmailLogTab" role="tabpanel">
                                            <!-- <div class="col-lg-12 col-md-12"> -->
                                            <div class="table-responsive m-t-10 pull-left" style="font-size: 12px;">
                                                <table id="example" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">

                                                    <thead>
                                                    <tr>
                                                        <th>Log Type</th>
                                                        <th><?php echo $this->lang->line('sender');?></th>
                                                        <th><?php echo $this->lang->line('subject');?></th>
                                                        <th><?php echo $this->lang->line('sent_date');?></th>
                                                        <th><?php echo $this->lang->line('status');?></th>
                                                        <th><?php echo $this->lang->line('body');?></th>
                                                        <th><?php echo $this->lang->line('attachment');?></th>
                                                        <th><?php echo $this->lang->line('action');?></th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <?php foreach($emailLogs as $emailLog){ ?>
                                                        <tr>
                                                            <td><?php echo $emailLog->log_type;
                                                                /*if($emailLog->log_type_id=='3'){ echo "Sent"; } else if($emailLog->log_type_id=='4'){ echo "Received"; }*/ ?></td>
                                                            <td><?php echo $emailLog->email_from;?></td>
                                                            <td><?php echo wordwrap($emailLog->email_subject, 35, "<br />\n");?></td>

                                                            <?php $mydate = strtotime($emailLog->date);
                                                            $newformat = date('d-m-Y h:i:s A',$mydate);
                                                            echo '  <td data-sort="'. $mydate .'">'.wordwrap($newformat, 12, "<br />\n") .'</td>'; ?>
                                                            <td id="email_status_<?php echo $emailLog->id;?>"><?php echo $emailLog->email_status;?></td>
                                                            <td><a style="color: blue;text-decoration: underline;" href="javascript:" onclick="showEmailBody(<?php echo $emailLog->id;?>);" >Body</a></td>
                                                            <td></td>
                                                            <td><a  onclick="open_in_new_tab_and_reload('<?php echo site_url(); ?>/incomingemails/reply_email/<?php echo $emailLog->id;?>')" style="font-size: 18px;margin-right: 6px;" title="Reply" href="javascript:" ><i class=" fa fa-mail-reply"></i></a></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                        <!-- End of Email Log Tab -->



                                        <!-- Start of SMS Log Tab -->
                                        <div class="tab-pane p-20 pull-left" id="SMSLogTab" role="tabpanel">
                                            <!-- <div class="col-lg-12 col-md-12"> -->
                                            <div class="table-responsive pull-left" style="font-size: 12px;">
                                                <table id="smsLogTable" class="example display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">

                                                    <thead>
                                                    <tr>
                                                        <th>Log Type</th>
                                                        <th>Mobile Number</th>
                                                        <th>Message Sent</th>
                                                        <th>Date</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <?php foreach($smsLogs as $smsLog){ ?>
                                                        <tr>
                                                            <td><?php if($smsLog->log_type_id=='8'){ echo "Sent"; } else { echo "Received"; } ?></td>
                                                            <td><?php echo $smsLog->mobile_number;?></td>
                                                            <td><?php echo $smsLog->message;?></td>
                                                            <?php $mydate = strtotime($smsLog->sent_time);
                                                            $newformat = date('d-m-Y h:i:s A',$mydate);
                                                            echo '  <td data-sort="'. $mydate .'">'.$newformat.'</td>'; ?>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                        <!-- End of SMS Log Tab -->

                                        <!-- Start of Dispatch Tab -->
                                        <div class="tab-pane p-20 pull-left" id="DispatchTab" role="tabpanel">

                                            <div class="col-lg-12 col-md-12 pull-left">
                                                <div class="form-group row">
                                                    <label class="col-2 col-form-label">Number of Legs</label>
                                                    <div class="col-3">
                                                        <input type="text" id="no_of_legs" name="no_of_legs" readonly="" class="form-control inpstyl" placeholder="No of Legs">
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <a class="btn waves-effect waves-light btn-outline-danger" href="<?php echo site_url().'/admin/addNewLeg/'.$job_id; ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_new_leg'); ?></a>
                                                    <!-- <button type="submit" class="btn waves-effect waves-light btn-outline-danger"> <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_new_leg'); ?></button> -->
                                                </div>
                                            </div>

                                            <div style="float: left;width: 66.5%;">
                                                <div class="table-responsive m-t-40 pull-left" style="font-size: 12px; border:1px solid lightgray;">
                                                    <table id="dispatchTable"  class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">

                                                        <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th style="width: 69px;">Leg #</th>
                                                            <th style="width: 120px;"><?php echo $this->lang->line('date'); ?></th>
                                                            <th style="width: 135px;"><?php echo $this->lang->line('address'); ?></th>
                                                            <!-- <th><?php //echo $this->lang->line('to');  ?></th> -->
                                                            <th style="width: 155px;"><?php echo $this->lang->line('vehicle'); ?></th>

                                                            <th style="width: 130px;"><?php echo $this->lang->line('contractor_payment'); ?></th>

                                                            <th style="width: 160px;"><?php echo $this->lang->line('job_type'); ?></th>
                                                            <th style="width: 130px;"><?php echo $this->lang->line('leg_status'); ?></th>

                                                            <th style="width: 130px;"><?php echo $this->lang->line('cbm'); ?></th>

                                                            <th style="width: 130px;"><?php echo $this->lang->line('customer_invoiced'); ?></th>

                                                            <th style="width: 130px;"><?php echo $this->lang->line('customer_payment'); ?></th>

                                                            <th style="width: 120px;"><?php echo $this->lang->line('dispatch_notes'); ?></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $no_of_legs = $job_data->no_of_legs;
                                                        //$i=1;
                                                        for ($i = 1; $i <= $no_of_legs; $i++) {
                                                            ?>
                                                            <tr>
                                                                <td><a class="btn btn-danger" data-toggle="modal" data-target="#deleteJobLegModal" onclick="deleteJobLeg(<?php echo $i; ?>)"><i class="fa fa-trash"></i></a></td>
                                                                <td><?php echo $i; ?></td>
                                                                <td>
                                                                    <input type="date" name="leg_date[]" id="leg_date_<?php echo $i; ?>" style="width: 132px;font-size: 11px;" class="form-control inpstyl user-success">
                                                                </td>
                                                                <td>
                                                                    <!-- <input type="text" style="font-size: 11px;" name="from_leg" id="from_leg" autocomplete="on" class="form-control inpstyl" placeholder="From"> -->
                                                                    <textarea id="from_leg_<?php echo $i; ?>" style="font-size: 11px;width:100px;"  placeholder="<?php echo $this->lang->line('from'); ?>" name="from_leg[]" cols="18" rows="3" class="form-control user-success"></textarea>
                                                                    <br/>
                                                                    <textarea id="to_leg_<?php echo $i; ?>" style="font-size: 11px;"  placeholder="<?php echo $this->lang->line('to'); ?>" name="to_leg[]" cols="18" rows="3" class="form-control user-success"></textarea>

                                                                </td>
                                                                <!-- <td>

                                                                </td> -->
                                                                <td>
                                                                    <select name="leg_vehicle_id[]" id="leg_vehicle_id_<?php echo $i; ?>" class="form-control" style="font-size: 11px;    width: 155px;    padding: 0px;">
                                                                        <option value="">Select Subcontractor</option>
                                                                        <?php
                                                                        $city = '';
                                                                        foreach ($vehicles as $value) {
                                                                            /*if ($city != $value->city_name) {
                                                                                if ($city != '') {
                                                                                    echo '</optgroup>';
                                                                                }
                                                                                echo '<optgroup label="' . ucfirst($value->city_name) . '">';
                                                                            }*/
                                                                            echo '<option value="' . $value->id . '">' . htmlspecialchars($value->name) . '</option>';
                                                                            $city = $value->city_name;
                                                                        }
                                                                        /*if ($city != '') {
                                                                            echo '</optgroup>';
                                                                        }*/
                                                                        ?>
                                                                    </select>
                                                                </td>

                                                                <td>

                                                                    <input type="number" min="0" step=0.01 name="leg_contractor_payment[]" id="leg_contractor_payment_<?php echo $i; ?>" style="width:123px;font-size: 11px;" class="form-control inpstyl user-success">

                                                                </td>
                                                                <td>
                                                                    <select name="leg_job_type[]" style="font-size: 11px;width: 77px;padding: 0px;" id="leg_job_type_<?php echo $i; ?>" class="form-control inpstyl phone-group" >
                                                                        <option value="">Job Type</option>
                                                                        <?php foreach ($job_types as $value) { ?>
                                                                            <option value="<?php echo $value->options; ?>"><?php echo $value->options; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td>

                                                                    <select name="leg_status[]" style="font-size: 11px;width: 136px;padding: 0px;" id="leg_status_<?php echo $i; ?>" class="form-control inpstyl phone-group" >
                                                                        <option value="">Job Type</option>
                                                                        <?php foreach ($legStatus as $value) { ?>
                                                                            <option value="<?php echo $value->options; ?>"><?php echo $value->options; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td>

                                                                    <input type="number" min="0" step=0.01 name="leg_cbm[]" id="leg_cbm_<?php echo $i; ?>" style="width:86px;font-size: 11px;" class="form-control inpstyl user-success">

                                                                </td>

                                                                <td>
                                                                    <select name="leg_customer_invoiced[]" style="font-size: 11px;width: 100px;" id="leg_customer_invoiced_<?php echo $i; ?>" class="form-control inpstyl phone-group" >
                                                                        <option value="No">No</option>
                                                                        <option value="Yes">Yes</option>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="number" min="0" step=0.01 name="leg_customer_payment[]" id="leg_customer_payment_<?php echo $i; ?>" style="width:118px;font-size: 11px;" class="form-control inpstyl user-success">

                                                                </td>
                                                                <td>
                                                                    <textarea id="leg_dispatch_notes_<?php echo $i; ?>" style="font-size: 11px;"  placeholder="<?php echo $this->lang->line('dispatch_notes'); ?>" name="leg_dispatch_notes[]" cols="31" rows="3" class="form-control user-success"></textarea>
                                                                </td>

                                                                <input type="hidden" name="leg_id[]" id="leg_id_<?php echo $i; ?>">

                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>

                                                </div>

                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>




                        <?php echo form_close(); ?>



                    </div>
                    <div class="col-xs-6 col-sm-3" style="border: 1px solid gray;border-left: 0px;padding: 0px;">
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="card" style="padding: 0px;">
                                <div class="card-body" style="padding: 0px;">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs customtab2 fixed-ul" id="nav-top-ul" role="tablist">
                                        <!-- style="width: 24%;" -->
                                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#job-log" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Job Logs</span></a> </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content tabcontent-border">
                                        <div class="tab-pane active" id="job-log" role="tabpanel"> <!-- style="margin-top: 54px;" -->
                                            <!-- <div class="p-20">

                                            </div> -->
                                            <ul id="job_logs" style="width: 100%;overflow-x: scroll;">
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Email Body Modal -->
                <div class="modal fade" id="emailLogBodyModal" tabindex="-1" role="dialog" aria-labelledby="emailLogBodyLabel" aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 790px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Email Body:</h4>
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $this->lang->line('close');?></span></button>

                            </div>
                            <div class="modal-body pull-left">  <!-- style="overflow-x: scroll;" -->

                                <span id="email_log_body_span" style="width: 100%; overflow-y: scroll;" class="pull-left"></span>
                                <h3 id="email_attachments_h3">Attachments</h3>
                                <span id="email_attachments_span" style="width: 100%; overflow-y: scroll;" class="pull-left"></span>
                            </div>
                            <div class="modal-footer">
                                <!--  <a type="button" class="btn btn-default" id="delete_no" href="<?php echo site_url(); ?>/admin/deleteStaff/Delete/<? echo $row->id; ?>"> <?php echo $this->lang->line('yes');?></a> -->
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="deleteJobLegModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $this->lang->line('close');?></span></button>
                                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('warning');?></h4>
                            </div>
                            <div class="modal-body">
                                <?php echo $this->lang->line('sure_delete');?>
                            </div>
                            <div class="modal-footer">
                                <a type="button" class="btn btn-default" id="delete_job_leg" href=""><?php echo $this->lang->line('yes');?></a>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(function () {
                        //$("#timepicker1,#timepicker3").val("");
                        var page_type = $("#page_type").val();
                        //alert(page_type);
                        //"<?php echo $type; ?>";
                        if(page_type!='edit'){
                            $('a[href="#BasicDetailTab"]').on('shown.bs.tab', function (e) {
                                //alert();
                                initMap();
                            });
                        }
                        $('#submit_job_btn, #submit_enquiry_btn').click(function () {
                            $(':required:invalid', '#add_new_job').each(function () {
                                var id = $('.tab-pane').find(':required:invalid').closest('.tab-pane').attr('id');
                                $('.nav a[href="#' + id + '"]').tab('show');
                            });
                        });
                        $("#submit_job_btn").click(function () {
                            $("#job_date").attr('required', '');
                            //$("#timepicker1").attr('required', '');
                            //$("#timepicker3").attr('required', '');
                            //$("#job_vehicle_id").attr('required', '');
                            $("#save_as").val('job');
                            //var action = '<?php echo site_url(); ?>'+'/admin/add_new_job_popup';
                            //$("#add_new_job").attr("action", action);
                        });

                        if(page_type!='edit'){
                            $('.modal-wide').on('shown.bs.modal', function () {
                                initMap();
                            });
                        }
                        $('.modal-wide').on('hide.bs.modal', function (e) {
                            var result = confirm("Are you sure you want to close the job?");
                            if (result) {
                                return true;
                            } else {
                                return false;
                            }
                        });
                        if(page_type!='edit'){
                            $('.modal-wide').on('shown.bs.hidden', function () {
                                initMap();
                            });
                        }
                        $("#tallModal").on("shown.bs.modal", function (e) {
                            google.maps.event.trigger(map, "resize");
                            //$('#tallModal').modal({backdrop: 'static', keyboard: false});
                            //return map.setCenter(thisLatLng);
                        });
                    });
                    $('#map-reload').click(function () {
                        $('#page_type').val('load');
                    });

                    if(page_type!='edit'){
                        $(document).ready(function () {
                            if(page_type=='add'){
                                google.maps.event.addDomListener(window, 'load', initMap);
                            } else {
                                var mapReloadBtn = document.getElementById('map-reload');
                                google.maps.event.addDomListener(mapReloadBtn, 'click', initMap);
                            }

                            //
                        });
                        function initMap() {
                            var update_rec_id = $('#update_rec_id').val
                            var page_type = "<?php echo $type; ?>";
                            //if(!update_rec_id){
                            var map = new google.maps.Map(document.getElementById('map'), {
                                mapTypeControl: false,
                                center: {lat: -33.8688, lng: 151.2195},
                                zoom: 13
                            });
                            //var map = new google.maps.Map(document.getElementById("map"));
                            var geocoder = new google.maps.Geocoder();
                            $("#city_id").change(function () {
                                address = $("#city_id :selected")[0].text;
                                geocodeAddress(address, geocoder, map);
                            });
                            var update_rec_id = $('#update_rec_id').val();
                            //var i=0;  && i==0
                            if (page_type == 'edit') { //update_rec_id
                                //alert(page_type);
                                var directionsServiceRoute = new google.maps.DirectionsService();
                                var directionsDisplayRoute = new google.maps.DirectionsRenderer();
                                var orig = "<?php echo $jobDetails->pickup_suburb ?>";//$('#pickup_address').val();
                                var dest = "<?php echo $jobDetails->delivery_suburb ?>";//$('#delivery_address').val();
                                directionsServiceRoute.route({
                                    origin: orig,
                                    destination: dest,
                                    travelMode: 'DRIVING', //this.travelMode
                                    avoidTolls: true
                                }, function (response, status) {
                                    if (status === 'OK') {
                                        //console.log(response);
                                        //console.log(response.routes[0].legs[0].distance);
                                        //console.log(response.routes[0].legs[0].duration.text);
                                        var totalDistance = response.routes[0].legs[0].distance.text;
                                        var totalDuration = response.routes[0].legs[0].duration.text;
                                        document.getElementById('totalDistance').innerHTML = totalDistance;
                                        document.getElementById('totalDuration').innerHTML = totalDuration;
                                        //me.directionsDisplay.setDirections({routes: []});
                                        /*google.maps.event.trigger(map, 'resize');
                                         directionsDisplayRoute.setMap(map);
                                         directionsDisplayRoute.setDirections(response);*/
                                        new google.maps.DirectionsRenderer({
                                            map: map,
                                            directions: response
                                        });
                                        //i=1;
                                        //directionsDisplayRoute.setDirections(response);
                                        //me.directionsDisplay.setMap(map);
                                    } else {
                                        //window.alert('Directions request failed due to ' + status);
                                    }
                                });
                            } //else {
                            var address = $("#city_id :selected")[0].text;
                            //}
                            //console.log(address);
                            geocodeAddress(address, geocoder, map);
                            new AutocompleteDirectionsHandler(map, update_rec_id);
                        }
                        //var update_rec_id    = $('#update_rec_id').val();
                        //if(update_rec_id){
                        //initMap();
                        //}
                        function geocodeAddress(address, geocoder, resultsMap) {
                            //document.getElementById('info').innerHTML = address;
                            //alert(address);
                            geocoder.geocode({
                                'address': address
                            }, function (results, status) {
                                if (status === google.maps.GeocoderStatus.OK) {
                                    resultsMap.fitBounds(results[0].geometry.viewport);
                                    //document.getElementById('info').innerHTML += "<br>" + results[0].geometry.location.toUrlValue(6);
                                } else {
                                    console.log('Geocode was not successful for the following reason: ' + status);
                                }
                            });
                        }
                        // Sets a listener on a radio button to change the filter type on Places
                        // Autocomplete.
                        AutocompleteDirectionsHandler.prototype.setupClickListener = function (id, mode) {
                            var radioButton = document.getElementById('delivery_suburb');
                            //document.getElementById(id);
                            var me = this;
                            radioButton.addEventListener('click', function () {
                                me.travelMode = mode;
                                me.route();
                            });
                        };
                        AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function (autocomplete, mode) {
                            var me = this;
                            autocomplete.bindTo('bounds', this.map);
                            autocomplete.addListener('place_changed', function () {
                                var place = autocomplete.getPlace();
                                //console.log(place); //ChIJ9-KIO3pc1moRP7B9Dof9Rg8
                                if (!place.place_id) {
                                    //window.alert("Please select an option from the dropdown list.");
                                    return;
                                }
                                if (mode === 'ORIG') {
                                    me.originPlaceId = place.place_id;
                                } else {
                                    me.destinationPlaceId = place.place_id;
                                }
                                //me.directionsDisplay.setMap(null);
                                me.route();
                            });
                        };
                        AutocompleteDirectionsHandler.prototype.route = function () {
                            if (!this.originPlaceId || !this.destinationPlaceId) {
                                return;
                            }
                            var me = this;
                            this.directionsService.route({
                                origin: {'placeId': this.originPlaceId},
                                destination: {'placeId': this.destinationPlaceId},
                                travelMode: 'DRIVING', //this.travelMode
                                avoidTolls: true
                            }, function (response, status) {
                                if (status === 'OK') {
                                    console.log(response);
                                    //console.log(response.routes[0].legs[0].distance);
                                    console.log(response.routes[0].legs[0].duration.text);
                                    var totalDistance = response.routes[0].legs[0].distance.text;
                                    var totalDuration = response.routes[0].legs[0].duration.text;
                                    document.getElementById('totalDistance').innerHTML = totalDistance;
                                    document.getElementById('totalDuration').innerHTML = totalDuration;
                                    me.directionsDisplay.setDirections(response);
                                    //me.directionsDisplay.setMap(map);
                                } else {
                                    window.alert('Directions request failed due to ' + status);
                                }
                            });
                        };
                        function getLatLongFromAddress(address)
                        {
                            var geocoder = new google.maps.Geocoder();
                            var address = address;
                            geocoder.geocode({'address': address}, function (results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var latitude = results[0].geometry.location.lat();
                                    var longitude = results[0].geometry.location.lng();
                                    var latlng = latitude + ',' + longitude;
                                    console.log('latlng=' + latlng);
                                    return latlng;
                                } else {
                                    return 0;
                                }
                            });
                        }
                        function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
                            directionsService.route({
                                origin: pointA,
                                destination: pointB,
                                travelMode: google.maps.TravelMode.DRIVING
                            }, function (response, status) {
                                if (status == google.maps.DirectionsStatus.OK) {
                                    directionsDisplay.setDirections(response);
                                } else {
                                    window.alert('Directions request failed due to ' + status);
                                }
                            });
                        }

                        /**
                         * @constructor
                         */
                        function AutocompleteDirectionsHandler(map, update_rec_id) {
                            this.map = map;
                            this.originPlaceId = null;
                            this.destinationPlaceId = null;
                            this.travelMode = 'DRIVING';
                            // if(!update_rec_id){
                            var originInput = document.getElementById('pickup_suburb');
                            var destinationInput = document.getElementById('delivery_suburb');
                            //} else {
                            //var originInput = $('#pickup_address').val();
                            //var destinationInput = $('#delivery_address').val();
                            //}
                            var modeSelector = document.getElementById('mode-selector');
                            this.directionsService = new google.maps.DirectionsService;
                            this.directionsDisplay = new google.maps.DirectionsRenderer;
                            //this.directionsDisplay.setMap(null);
                            google.maps.event.trigger(map, 'resize');
                            this.directionsDisplay.setMap(map);
                            var options = {
                                types: ['(cities)'],
                                componentRestrictions: {country: "au"}
                            };
                            var originAutocomplete = new google.maps.places.Autocomplete(
                                originInput, options);
                            var destinationAutocomplete = new google.maps.places.Autocomplete(
                                destinationInput, options); //, {placeIdOnly: true}
                            //console.log(originAutocomplete);
                            document.addEventListener('DOMNodeInserted', function (event) {
                                var target = $(event.target);
                                if (target.hasClass('pac-item')) {
                                    target.html(target.html().replace(/, Australia<\/span>$/, "</span>"));
                                }
                            });
                            /*this.setupClickListener('changemode-walking', 'WALKING');
                             this.setupClickListener('changemode-transit', 'TRANSIT');*/
                            this.setupClickListener('changemode-driving', 'DRIVING');
                            this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
                            this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');
                            //this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
                            //this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(destinationInput);
                            //this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
                        }
                    } //end of if page!=edit
                    //initMap();
                </script>

                <?php
                $googleMapsApi = getApiSettings('Google Maps');
                $page_type = $type;
                //if($page_type!='edit'){
                //AIzaSyDPulD1c-_nSIqdOo5qUPHgXKPGQynWzhU   //key
                ?>

                <?php
                $page_type = $type;
                if($page_type=='edit'){
                    ?>
                    <script type="text/javascript">
                        var locationID = 'pickup_suburb';
                        var locationID2 = 'delivery_suburb';
                        function initMap() {
                            var options = {
                                types: ['(cities)'],
                                componentRestrictions: {country: "au"}
                            };
                            var input = document.getElementById(locationID);
                            var autocomplete = new google.maps.places.Autocomplete(input, options);
                            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                                var place = autocomplete.getPlace();
                            });
                            var input2 = document.getElementById(locationID2);
                            var autocomplete2 = new google.maps.places.Autocomplete(input2, options);
                            google.maps.event.addListener(autocomplete2, 'place_changed', function () {
                                var place = autocomplete.getPlace();
                            });
                        }
                        document.addEventListener('DOMNodeInserted', function (event) {
                            var target = $(event.target);
                            if (target.hasClass('pac-item')) {
                                target.html(target.html().replace(/, Australia<\/span>$/, "</span>"));
                            }
                        });
                    </script>
                <?php } ?>

                <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApi->account_key; ?>&v=3.exp&libraries=places&region=AU&callback=initMap"></script>
                <?php //} ?>



                <script type="text/javascript">
                    function scroll_style() {
                        var window_top = $(window).scrollTop();
                        console.log(window_top);
                        var div_top = $('#sidebarnav').offset().top;
                        console.log(div_top);
                        if (window_top > 48) {
                            $('#book-job-header').addClass("fix-header-top");
                            $('.customtab2').addClass("fixed-ul-top");
                            //$('.customtab2').removeClass("fixed-ul");
                        } else {
                            $('#book-job-header').removeClass("fix-header-top");
                            //$('.customtab2').addClass("fixed-ul");
                            $('.customtab2').removeClass("fixed-ul-top");
                        }
                    }
                    $(function () {
                        // $(window).scroll(scroll_style);
                        //scroll_style();
                    });
                    $(document).ready(function () {
                        var pacContainerInitialized = false;
                        $('#pickup_suburb').keypress(function () {
                            if (!pacContainerInitialized) {
                                $('.pac-container').css('z-index', '9999');
                                pacContainerInitialized = true;
                            }
                        });
                        var pacContainerInitialized = false;
                        $('#delivery_suburb').keypress(function () {
                            if (!pacContainerInitialized) {
                                $('.pac-container').css('z-index', '9999');
                                pacContainerInitialized = true;
                            }
                        });
                    });
                </script>
                <script src="<?php echo base_url(); ?>/assets/eliteadmin/node_modules/dropify/dist/js/dropify.min.js"></script>
                <script>
                    $(document).ready(function () {
                        // Basic
                        $('.dropify').dropify();
                        // Used events
                        var drEvent = $('#input-file-events').dropify();
                        drEvent.on('dropify.beforeClear', function (event, element) {
                            return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
                        });
                        drEvent.on('dropify.afterClear', function (event, element) {
                            alert('File deleted');
                        });
                        drEvent.on('dropify.errors', function (event, element) {
                            console.log('Has Errors');
                        });
                        var drDestroy = $('#input-file-to-destroy').dropify();
                        drDestroy = drDestroy.data('dropify')
                        $('#toggleDropify').on('click', function (e) {
                            e.preventDefault();
                            if (drDestroy.isDropified()) {
                                drDestroy.destroy();
                            } else {
                                drDestroy.init();
                            }
                        })
                    });
                </script>


                <!-- Email Body Modal -->
                <div class="modal fade" id="emailBodyModal" tabindex="-1" role="dialog" aria-labelledby="emailBodyModalLabel" aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 790px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Email Body:</h4>
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $this->lang->line('close');?></span></button>

                            </div>
                            <div class="modal-body pull-left">  <!-- style="overflow-x: scroll;" -->

                                <span id="email_body_span" style="width: 100%; overflow-y: scroll;" class="pull-left"></span>
                            </div>
                            <div class="modal-footer">
                                <!--  <a type="button" class="btn btn-default" id="delete_no" href="<?php echo site_url(); ?>/admin/deleteStaff/Delete/<? echo $row->id; ?>"> <?php echo $this->lang->line('yes');?></a> -->
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="deleteLogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo $this->lang->line('close'); ?></span></button>
                                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('warning'); ?></h4>
                            </div>
                            <div class="modal-body">
                                <?php echo $this->lang->line('sure_delete'); ?>
                            </div>
                            <div class="modal-footer">
                                <a type="button" class="btn btn-default" id="delete_no" onclick="deleteJobLogAjax();" href="javascript:"> <?php echo $this->lang->line('yes'); ?></a>
                                <input type="hidden" name="log_id" id="log_id">
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function changeDeleteId(log_id, job_id) {
                        //var str = "<?php echo site_url(); ?>/admin/deleteJobLog/" + log_id +"/"+ job_id + "/book_job";
                        $("#log_id").val(log_id);
                        //$("#delete_no").attr("href",str);
                    }
                    function deleteJobLogAjax()
                    {
                        var job_id = "<?php echo $job_id; ?>";
                        var log_id = $("#log_id").val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "<?php echo site_url(); ?>/admin/delete_job_log_ajax",
                            data: {"job_id": job_id, "log_id": log_id,
                                '<?php echo $this->security->get_csrf_token_name(); ?>':
                                    '<?php echo $this->security->get_csrf_hash(); ?>'
                            },
                            success: function (data) {
                                $('#alert-message').removeClass("alert-danger").addClass("alert-success");
                                $('#alert-message').html("Log deleted successfully.");
                                $("#alert-message").show().delay(2000).fadeOut();
                                $('#log_' + log_id).remove();
                                $('#deleteLogModal').modal('hide');
                            },
                            error: function (xhr, status, error) {
                                $("#alert-message").removeClass("alert-success").addClass("alert-danger");
                                $('#alert-message').html("Log not deleted. Please try again.");
                                $("#alert-message").show().delay(2000).fadeOut();
                            }
                        });
                    }
                    $(document).ready(function() {
                        if (location.hash) {
                            //alert(location.hash);
                            $('#selected_tab').val(location.hash);
                            $("a[href='" + location.hash + "']").tab("show");
                            //$("a[href='" + location.hash + "']").addClass("active");
                        } else {
                            //alert('<?php //echo $this->uri->segment(4); ?>');
                            <?php if($this->uri->segment(4)=='edit'){ ?>
                            $("a[href='SummaryTab']").tab("show");
                            $("a[href='SummaryTab']").addClass("active");
                            $('#SummaryTab').addClass("active");
                            $('#selected_tab').val('#SummaryTab');
                            <?php } ?>
                        }
                        $(document.body).on("click", "a[data-toggle='tab']", function(event) {
                            location.hash = this.getAttribute("href");
                            $('#selected_tab').val(location.hash);
                        });
                    });
                    $(window).on("popstate", function() {
                        /*var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
                        $("a[href='" + anchor + "']").tab("show");*/
                    });
                    function showEmailBody(log_id)
                    {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            cache: false,
                            async: false,
                            url: "<?php echo site_url(); ?>/incomingemails/getLogDetailsAjax",
                            data: {"log_id":log_id,
                                '<?php echo $this->security->get_csrf_token_name(); ?>' :
                                    '<?php echo $this->security->get_csrf_hash(); ?>'
                            },
                            success: function(response){
                                //console.log(response);
                                //if(response){
                                $('#email_body_span').html('');
                                if(response.log_details){
                                    $('#email_body_span').html(response.log_details);
                                } else {
                                    $('#email_body_span').html('No Email body');
                                }

                                $('#emailBodyModal').modal('show');

                                //}

                            },
                            error: function (error) {
                                //console.log(error);
                                //var err = eval(error);
                                console.log(error);
                            }

                        }); //ajax call
                    }
                    function open_in_new_tab_and_reload(url)
                    {
                        //Open in new tab
                        window.open(url, '_blank');
                        //focus to thet window
                        window.focus();
                        var log_id = url.substring(url.lastIndexOf('/') + 1)
                        $('#email_status_'+log_id).html('Read');
                        //reload current page
                        //location.reload();
                    }
                </script>

