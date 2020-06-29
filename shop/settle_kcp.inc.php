<?
if (!defined("_GNUBOARD_")) exit; // ���� ������ ���� �Ұ� 

include("./settle_kcp.web.standard.inc.php");
return;

$test = "";
if ($default['de_card_test']) {
    if ($default['de_escrow_use'] == 1) { 
        // ����ũ�ΰ��� �׽�Ʈ
        $default['de_kcp_mid'] = "T0007";        
    } 
    else { 
        // �Ϲݰ��� �׽�Ʈ
        $default['de_kcp_mid'] = "T0000";        
    }

    $test = "_test";
}
else {
    $default['de_kcp_mid'] = "SR".$default['de_kcp_mid'];
}

if (strtolower($g4['charset']) == 'utf-8')
    $js_url = "https://pay.kcp.co.kr/plugin/payplus{$test}_un.js";
else
    $js_url = "https://pay.kcp.co.kr/plugin/payplus{$test}.js";

/*
 * hashdata ��ȣȭ (�������� ������)
 *
 * hashdata ��ȣȭ ����( site_cd + ordr_idxx + good_mny + timestamp + server_key )
 * site_cd : ����Ʈ�ڵ�
 * ordr_idxx : �ֹ���ȣ
 * good_mny : �����ݾ� 
 * timestamp : Ÿ�ӽ�����
 * server_key : ����Ű
 */   

$site_cd   = trim($default['de_kcp_mid']);
$ordr_idxx = trim($od['od_id']);
$good_mny  = (int)$settle_amount;
$timestamp = $g4['server_time'];
$serverkey = $_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_ADDR']; // ����ڰ� �˼� ���� ������ ����
$hashdata = md5($site_cd.$ordr_idxx.$good_mny.$timestamp.$serverkey);
?>

<script type="text/javascript" src="<?=$js_url?>"></script>
<script type="text/javascript">
// �÷����� ��ġ(Ȯ��)
StartSmartUpdate();

/*
function OpenWindow() 
{
    var form = document.order_info;

    if( document.Payplus.object == null ) {
        openwin = window.open( './kcp/chk_plugin.php', 'chk_plugin', 'width=420, height=100, top=300, left=300' );
    }

    if ( MakePayMessage( form ) == true ) {
        openwin = window.open( './kcp/proc_win.php', 'proc_win', 'width=420, height=100, top=300, left=300' );
        form.submit();
    }
}
*/

/* Payplus Plug-in ���� */
function  jsf__pay( form )
{
    var RetVal = false;

    /* Payplus Plugin ���� */
    if ( MakePayMessage( form ) == true )
    {
        //openwin = window.open( './kcp/proc_win.php', 'proc_win', 'width=420, height=100, top=300, left=300' );
        openwin = window.open( './kcp/proc_win.php', 'proc_win', 'width=420, height=100, top=300, left=300' );
        RetVal = true ;
    }
    
    else
    {
        /*  res_cd�� res_msg������ �ش� �����ڵ�� �����޽����� �����˴ϴ�.
            ex) ���� Payplus Plugin���� ��� ��ư Ŭ���� res_cd=3001, res_msg=����� ���
            ���� �����˴ϴ�.
        */
        res_cd  = document.order_info.res_cd.value ;
        res_msg = document.order_info.res_msg.value ;

    }

    return RetVal ;
}
</script>

<form name="order_info" method="post" action='./kcp/pp_ax_hub.php'>
<!-- ����� ���� -->
<input type=hidden name='hashdata'      value='<?=$hashdata?>'>
<input type=hidden name='timestamp'     value='<?=$timestamp?>'>
<input type=hidden name='d_url'         value='<?=$g4['url']?>'>
<input type=hidden name='shop_dir'      value='<?=$g4['shop']?>'>
<input type=hidden name='on_uid'        value='<?=$_SESSION['ss_temp_on_uid']?>'>

<?
switch ($settle_case)
{
    case '������ü' :
        $settle_method = "010000000000";
        break;
    case '�������' :
        $settle_method = "001000000000";
        break;
    case '�޴���' :
        $settle_method = "000010000000";
        break;
    default : // �ſ�ī��
        $settle_method = "100000000000";
        break;
}
?>
<!-- 
    2012.08.18 ���� ���� ����Ǵ� '���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ����'�� ���� �ڵ� ����
    �̿�Ⱓ�� ���ѵǴ� ������ ��ǰ�̳� ���� ���� ��ǰ � ���Ͽ� '�뿪�� �����Ⱓ'�� 
    ǥ��/�����Ͽ��� �ϸ� �̿� ������ �ǹ� ��ۻ�ǰ ���� �������� �ش���� �ʽ��ϴ�. 
    0 : �Ϲݰ���
    good_expr�� ������ ���� ��Ŀ� ���ؼ��� KCP���� �����ϴ� �Ŵ����� ������ �ּ���.
-->
<input type=hidden name='good_expr'     value='0'>

<input type=hidden name='pay_method'    value='<?=$settle_method?>'>
<input type=hidden name='currency'      value='WON'>
<input type=hidden name='good_name'     value='<?=$goods?>'>
<input type=hidden name='good_mny'      value='<?=$good_mny?>'>
<input type=hidden name='buyr_name'     value='<?=addslashes($od['od_name'])?>' >
<input type=hidden name='buyr_mail'     value='<?=$od['od_email']?>'>
<input type=hidden name='buyr_tel1'     value='<?=$od['od_tel']?>'>
<input type=hidden name='buyr_tel2'     value='<?=$od['od_hp']?>'>

<input type=hidden name='quotaopt'      value='12'>

<input type=hidden name='rcvr_name'     value='<?=addslashes($od['od_b_name'])?>'>
<input type=hidden name='rcvr_tel1'     value='<?=$od['od_b_tel']?>'>
<input type=hidden name='rcvr_tel2'     value='<?=$od['od_b_hp']?>'>
<input type=hidden name='rcvr_mail'     value='<?=$od['od_email']?>'>
<input type=hidden name='rcvr_zipx'     value='<?=$od['od_b_zip1'].$od['od_b_zip2']?>'>
<input type=hidden name='rcvr_add1'     value='<?=addslashes($od['od_b_addr1'])?>'>
<input type=hidden name='rcvr_add2'     value='<?=addslashes($od['od_b_addr2'])?>'>

<?
$good_info = "";
$sql = " select a.ct_id,
                a.it_opt1,
                a.it_opt2,
                a.it_opt3,
                a.it_opt4,
                a.it_opt5,
                a.it_opt6,
                a.ct_amount,
                a.ct_point,
                a.ct_qty,
                a.ct_status,
                b.it_id,
                b.it_name,
                b.ca_id
           from $g4[yc4_cart_table] a, 
                $g4[yc4_item_table] b
          where a.on_uid = '$s_on_uid'
            and a.it_id  = b.it_id
          order by a.ct_id ";
$result = sql_query($sql);
for ($i=1; $row=mysql_fetch_array($result); $i++) 
{
    if ($i>1)
        $good_info .= chr(30);
    $good_info .= "seq=".$i.chr(31);
    $good_info .= "ordr_numb={$ordr_idxx}_".sprintf("%04d", $i).chr(31);
    //$good_info .= "good_name=".addslashes($row['it_name']).chr(31);
    $good_info .= "good_name=".preg_replace("/[[:punct:]]/", "", $row['it_name']).chr(31);
    $good_info .= "good_cntx=".$row['ct_qty'].chr(31);
    $good_info .= "good_amtx=".$row['ct_amount'].chr(31);
}
?>

<!-- �ʼ� �׸� -->

<!-- ��û���� ����(pay)/���,����(mod) ��û�� ��� -->
<input type='hidden' name='req_tx'    value='pay'>
<!-- �׽�Ʈ ������ : T0007 ���� ����, ���� ������ : �ο����� ����Ʈ�ڵ� �Է� -->
<input type='hidden' name='site_cd'   value='<?=$site_cd?>'>

<!-- MPI ����â���� ��� �ѱ� ��� �Ұ� -->
<input type='hidden' name='site_name' value='<?=$default[de_admin_company_name]?>'>

<!-- �ʼ� �׸� : PULGIN ���� ���� �������� ������ -->
<input type='hidden' name='module_type' value='01'>

<input type='hidden' name='ordr_idxx' value='<?=$ordr_idxx?>'>

<!-- ����ũ�� �׸� -->

<!-- ����ũ�� ��� ���� : �ݵ�� Y �� ���� -->
<input type='hidden' name='escw_used' value='Y'>

<!-- ����ũ�� ����ó�� ��� : ����ũ��: Y, �Ϲ�: N, KCP ���� ����: O -->
<input type='hidden' name='pay_mod' value='<?=($default[de_escrow_use]?"O":"N");?>'>

<!-- ��� �ҿ��� : ���� ��� �ҿ����� �Է� -->
<input type='hidden' name='deli_term' value='03'>

<!-- ��ٱ��� ��ǰ ���� : ��ٱ��Ͽ� ����ִ� ��ǰ�� ������ �Է� -->
<input type='hidden' name='bask_cntx' value='<?=(int)($goods_count+1)?>'>

<!-- ��ٱ��� ��ǰ �� ���� (�ڹ� ��ũ��Ʈ ����(create_goodInfo()) ����) -->
<input type='hidden' name='good_info' value='<?=$good_info?>'>

<!-- �ʼ� �׸� : PLUGIN���� ���� �����ϴ� �κ����� �ݵ�� ���ԵǾ�� �մϴ�. �ؼ������� ���ʽÿ�.-->
<input type='hidden' name='res_cd'         value=''>
<input type='hidden' name='res_msg'        value=''>
<input type='hidden' name='tno'            value=''>
<input type='hidden' name='trace_no'       value=''>
<input type='hidden' name='enc_info'       value=''>
<input type='hidden' name='enc_data'       value=''>
<input type='hidden' name='ret_pay_method' value=''>
<input type='hidden' name='tran_cd'        value=''>
<input type='hidden' name='bank_name'      value=''>
<input type='hidden' name='use_pay_method' value=''>

<!-- ���ݿ����� ��� â�� ��� ���� ���� - 5000�� �̻� �ݾ׿��� �������� �˴ϴ�.-->
<input type="hidden" name="disp_tax_yn"     value="N">
<!-- ���ݿ����� ���� ���� : PLUGIN ���� �����޴� �����Դϴ� -->
<input type="hidden" name="cash_tsdtime"    value="">
<input type="hidden" name="cash_yn"         value="">
<input type="hidden" name="cash_authno"     value="">
<input type="hidden" name="cash_tr_code"    value="">
<input type="hidden" name="cash_id_info"    value="">

<!-- ������ü ���񽺻� ���� -->
<input type="hidden" name="bank_issu"       value="">

<!-- ������ ���� -->
<input type="hidden" name="kcp_noint"       value="N">
<input type="hidden" name="kcp_noint_quota" value="">

<!-- �������� ī�� -->
<input type="hidden" name="not_used_card"   value="">

<p align="center"><input type="image" src="<?=$g4['shop_img_path']?>/btn_settle.gif" border="0"  onclick="return jsf__pay(this.form);" /></p>
</form>