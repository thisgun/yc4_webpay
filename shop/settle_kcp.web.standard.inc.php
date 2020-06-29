<?php
if (!defined("_GNUBOARD_")) exit; // ���� ������ ���� �Ұ�

if ($default['de_card_test']) {
    if ($default['de_escrow_use'] == 1) {
        // ����ũ�ΰ��� �׽�Ʈ
        $default['de_kcp_mid'] = "T0007";
        $default['de_kcp_site_key'] = '4Ho4YsuOZlLXUZUdOxM1Q7X__';
    }
    else {
        // �Ϲݰ��� �׽�Ʈ
        $default['de_kcp_mid'] = "T0000";
        $default['de_kcp_site_key'] = '3grptw1.zW0GSo4PQdaGvsF__';
    }

    $g_conf_js_url = 'https://testpay.kcp.co.kr/plugin/payplus_web.jsp';
}
else {
    $default['de_kcp_mid'] = "SR".$default['de_kcp_mid'];
    $g_conf_js_url = 'https://pay.kcp.co.kr/plugin/payplus_web.jsp';
}

$g_conf_home_dir  = $g4['shop_path'].'/kcp';
$g_conf_key_dir   = '';

/*=======================================================================
 KCP ����ó�� �α����� ������ ���� �α� ���丮 ���� ��θ� �����մϴ�.
 �α� ������ ��δ� ������ ������ �� ���� ��θ� ������ �ֽʽÿ�.
 ��īƮ5�� config.php ������ �����ϴ� ��ΰ� /home/youngcart5/www ���
 �α� ���丮�� /home/youngcart5/log ������ �����ϼž� �մϴ�.
 �α� ���丮�� ���� ������ �־�� �α� ������ �����˴ϴ�.
=======================================================================*/
$g_conf_log_dir   = '/home100/kcp'; // �������� �ʴ� ��θ� �Է��Ͽ� �α� ���� �������� �ʵ��� ��.

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
{
    $g_conf_key_dir   = $g4['shop_path'].'/kcp/bin/pub.key';
}

$g_conf_site_cd  = $default['de_kcp_mid'];
$g_conf_site_key = $default['de_kcp_site_key'];

if (preg_match("/^T000/", $g_conf_site_cd)) {
    $g_conf_gw_url  = "testpaygw.kcp.co.kr";                    // real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
}
else {
    $g_conf_gw_url  = "paygw.kcp.co.kr";
}

// KCP SITE KEY �Է� üũ
if($default['de_iche_use'] || $default['de_vbank_use'] || $default['de_card_use']) {
    if(trim($default['de_kcp_site_key']) == '')
        alert('KCP SITE KEY�� �Է��� �ֽʽÿ�.');
}

$g_conf_site_name = $default['de_admin_company_name'];
$g_conf_log_level = '3';           // ����Ұ�
$g_conf_gw_port   = '8090';        // ��Ʈ��ȣ(����Ұ�)
$module_type      = '01';          // ����Ұ�

$site_cd   = trim($default['de_kcp_mid']);
$ordr_idxx = trim($od['od_id']);
$good_mny  = (int)$settle_amount;
$timestamp = $g4['server_time'];
$serverkey = $_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_ADDR']; // ����ڰ� �˼� ���� ������ ����
$hashdata = md5($site_cd.$ordr_idxx.$good_mny.$timestamp.$serverkey);

if( ! ($default['de_iche_use'] || $default['de_vbank_use'] || $default['de_card_use']) ){
    return;
}
?>
<script type="text/javascript">
/****************************************************************/
/* m_Completepayment  ����                                      */
/****************************************************************/
/* �����Ϸ�� ��� �Լ�                                         */
/* �ش� �Լ����� ���� �����ϸ� �ȵ˴ϴ�.                        */
/* �ش� �Լ��� ��ġ�� payplus.js ���ٸ��� ����Ǿ �մϴ�.    */
/* Web ����� ��� ���� ���� form ���� �Ѿ��                   */
/* EXE ����� ��� ���� ���� json ���� �Ѿ��                   */
/****************************************************************/
function m_Completepayment( FormOrJson, closeEvent )
{
    var frm = document.order_info;

    /********************************************************************/
    /* FormOrJson�� ������ ���� Ȱ�� ����                               */
    /* frm ���� FormOrJson ���� ���� �� frm ������ Ȱ�� �ϼž� �˴ϴ�.  */
    /* FormOrJson ���� Ȱ�� �Ͻ÷��� ������������� ���ǹٶ��ϴ�.       */
    /********************************************************************/
    GetField( frm, FormOrJson );

    if( frm.res_cd.value == "0000" )
    {
        /*
        alert("���� ���� ��û ��,\n\n�ݵ�� ����â���� ������ ���� ���� �Ϸ� ��\n\n���� ���� ordr_chk �� ��ü �� �ֹ�������\n\n�ٽ� �ѹ� ���� �� ���� ���� ��û�Ͻñ� �ٶ��ϴ�."); //��ü ���� �� �ʼ� Ȯ�� ����.
        */
        /*
            ������ ���ϰ� ó�� ����
        */
        //document.getElementById("display_pay_button").style.display = "none" ;
        //document.getElementById("display_pay_process").style.display = "" ;

        frm.submit();
    }
    else
    {
        alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );

        closeEvent();
    }
}
</script>

<script src="<?php echo $g_conf_js_url; ?>"></script>
<script>
/* Payplus Plug-in ���� */
function jsf__pay( form )
{
    try
    {
        KCP_Pay_Execute( form );
    }
    catch (e)
    {
        /* IE ���� ���� ��������� throw�� ��ũ��Ʈ ���� */
    }

    return false;
}
</script>
<?php
    /* ============================================================================== */
    /* =   2. ������ �ʼ� ���� ����                                                 = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ʼ� - ������ �ݵ�� �ʿ��� �����Դϴ�.                               = */
    /* = -------------------------------------------------------------------------- = */
    // ��û���� : ����(pay)/���,����(mod) ��û�� ���
?>
    <form name="order_info" method="post" action='./kcp/pp_ax_hub.php'>

    <input type=hidden name="hashdata"      value="<?php echo $hashdata; ?>">
    <input type=hidden name="timestamp"     value="<?php echo $timestamp; ?>">
    <input type=hidden name="d_url"         value="<?php echo $g4['url']; ?>">
    <input type=hidden name="shop_dir"      value="<?php echo $g4['shop']; ?>">
    <input type=hidden name="on_uid"        value="<?php echo $_SESSION['ss_temp_on_uid']; ?>">

    <input type="hidden" name="req_tx"          value="pay">
    <input type="hidden" name="site_cd"         value="<?php echo $site_cd; ?>">
    <input type="hidden" name="site_name"       value="<?php echo $g_conf_site_name; ?>">
    <input type="hidden" name="def_site_cd"     value="<?php echo $site_cd; ?>">

<?php
    /*
    �Һοɼ� : Payplus Plug-in���� ī������� �ִ�� ǥ���� �Һΰ��� ���� �����մϴ�.(0 ~ 18 ���� ���� ����)
    �� ����  - �Һ� ������ �����ݾ��� 50,000�� �̻��� ��쿡�� ����, 50000�� �̸��� �ݾ��� �Ͻúҷθ� ǥ��˴ϴ�
               ��) value ���� "5" �� �������� ��� => ī������� ����â�� �ϽúҺ��� 5�������� ���ð���
    */
switch ($settle_case)
{
    case '������ü' :
        $settle_method = "010000000000";
        break;
    case '�������' :
        $settle_method = "001000000000";
        break;
    default : // �ſ�ī��
        $settle_method = "100000000000";
        break;
}
?>
    <input type="hidden" name="pay_method"  value="<?php echo $settle_method; ?>">
    <input type="hidden" name="ordr_idxx"   value="<?php echo $ordr_idxx; ?>">
    <input type="hidden" name="good_name"   value="<?php echo $goods; ?>">
    <input type="hidden" name="good_mny"    value="<?php echo $good_mny; ?>">
    <input type="hidden" name="buyr_name"   value="<?php echo addslashes($od['od_name'])?>">
    <input type="hidden" name="buyr_mail"   value="<?php echo $od['od_email']?>">
    <input type="hidden" name="buyr_tel1"   value="<?php echo $od['od_tel']?>">
    <input type="hidden" name="buyr_tel2"   value="<?php echo $od['od_hp']?>">

    <input type="hidden" name="rcvr_name"     value="<?php echo addslashes($od['od_b_name'])?>">
    <input type="hidden" name="rcvr_tel1"     value="<?php echo $od['od_b_tel']?>">
    <input type="hidden" name="rcvr_tel2"     value="<?php echo $od['od_b_hp']?>">
    <input type="hidden" name="rcvr_mail"     value="<?php echo $od['od_email']?>">
    <input type="hidden" name="rcvr_zipx"     value="<?php echo $od['od_b_zip1'].$od['od_b_zip2']?>">
    <input type="hidden" name="rcvr_add1"     value="<?php echo addslashes($od['od_b_addr1'])?>">
    <input type="hidden" name="rcvr_add2"     value="<?php echo addslashes($od['od_b_addr2'])?>">

    <input type="hidden" name="payco_direct"   value="">      <!-- PAYCO ����â ȣ�� -->

    <input type="hidden" name="quotaopt"    value="12">

    <!-- �ʼ� �׸� : ���� �ݾ�/ȭ����� -->
    <input type="hidden" name="currency"    value="WON">
<?php
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
           from {$g4['yc4_cart_table']} a, 
           {$g4['yc4_item_table']} b
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
    $good_info .= "good_name=".addslashes($row['it_name']).chr(31);
    $good_info .= "good_cntx=".$row['ct_qty'].chr(31);
    $good_info .= "good_amtx=".$row['ct_amount'].chr(31);
}
    /* = -------------------------------------------------------------------------- = */
    /* =   2. ������ �ʼ� ���� ���� END                                             = */
    /* ============================================================================== */
    /* ============================================================================== */
    /* =   3. Payplus Plugin �ʼ� ����(���� �Ұ�)                                   = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ �ʿ��� �ֹ� ������ �Է� �� �����մϴ�.                            = */
    /* = -------------------------------------------------------------------------- = */
?>
    <!-- PLUGIN ���� �����Դϴ�(���� �Ұ�) -->
    <input type="hidden" name="module_type"     value="01">
    <!-- ���� ����Ʈ ������ �Ѿ���� ����Ʈ�� �ڵ� : OKĳ����(SCSK), �����Ǿ� ��������Ʈ(SCWB) -->
    <input type="hidden" name="epnt_issu"       value="">
<!--
      �� �� ��
          �ʼ� �׸� : Payplus Plugin���� ���� �����ϴ� �κ����� �ݵ�� ���ԵǾ�� �մϴ�
          ���� �������� ���ʽÿ�
-->
    <input type="hidden" name="res_cd"          value="">
    <input type="hidden" name="res_msg"         value="">
    <input type="hidden" name="tno"             value="">
    <input type="hidden" name="trace_no"        value="">
    <input type="hidden" name="enc_info"        value="">
    <input type="hidden" name="enc_data"        value="">
    <input type="hidden" name="ret_pay_method"  value="">
    <input type="hidden" name="tran_cd"         value="">
    <input type="hidden" name="bank_name"       value="">
    <input type="hidden" name="bank_issu"       value="">
    <input type="hidden" name="use_pay_method"  value="">

    <!--  ���ݿ����� ���� ���� : Payplus Plugin ���� �����ϴ� �����Դϴ� -->
    <input type="hidden" name="cash_tsdtime"    value="">
    <input type="hidden" name="cash_yn"         value="">
    <input type="hidden" name="cash_authno"     value="">
    <input type="hidden" name="cash_tr_code"    value="">
    <input type="hidden" name="cash_id_info"    value="">

    <!-- 2012�� 8�� 18�� ���ڻ�ŷ��� ���� ���� ���� �κ� -->
    <!-- ���� �Ⱓ ���� 0:��ȸ�� 1:�Ⱓ����(ex 1:2012010120120131)  -->
    <!--
        2012.08.18 ���� ���� ����Ǵ� '���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ����'�� ���� �ڵ� ����
        �̿�Ⱓ�� ���ѵǴ� ������ ��ǰ�̳� ���� ���� ��ǰ � ���Ͽ� '�뿪�� �����Ⱓ'��
        ǥ��/�����Ͽ��� �ϸ� �̿� ������ �ǹ� ��ۻ�ǰ ���� �������� �ش���� �ʽ��ϴ�.
        0 : �Ϲݰ���
        good_expr�� ������ ���� ��Ŀ� ���ؼ��� KCP���� �����ϴ� �Ŵ����� ������ �ּ���.
    -->
    <input type="hidden" name="good_expr" value="0">

    <!-- ���������� �����ϴ� �� ���̵� ������ �ؾ� �մϴ�.(�ʼ� ����) -->
	<input type="hidden" name="shop_user_id"    value=""/>
	<!-- ��������Ʈ ������ �������� �Ҵ�Ǿ��� �ڵ� ���� �Է��ؾ��մϴ�.(�ʼ� ����) -->
    <input type="hidden" name="pt_memcorp_cd"   value=""/>

    <!-- ����ũ�� �׸� -->

    <!-- ����ũ�� ��� ���� : �ݵ�� Y �� ���� -->
    <input type="hidden" name="escw_used" value="Y">

    <!-- ����ũ�� ����ó�� ��� : ����ũ��: Y, �Ϲ�: N, KCP ���� ����: O -->
    <input type="hidden" name="pay_mod" value="O">

    <!-- ��� �ҿ��� : ���� ��� �ҿ����� �Է� -->
    <input type="hidden" name="deli_term" value="03">

    <!-- ��ٱ��� ��ǰ ���� : ��ٱ��Ͽ� ����ִ� ��ǰ�� ������ �Է� -->
    <input type="hidden" name="bask_cntx" value="<?php echo (int)$goods_count + 1; ?>">

    <!-- ��ٱ��� ��ǰ �� ���� (�ڹ� ��ũ��Ʈ ����(create_goodInfo()) ����) -->
    <input type="hidden" name="good_info" value="<?php echo $good_info; ?>">

<?php
    /* = -------------------------------------------------------------------------- = */
    /* =   3. Payplus Plugin �ʼ� ���� END                                          = */
    /* ============================================================================== */
    /* ============================================================================== */
    /* =   4. �ɼ� ����                                                             = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ɼ� - ������ �ʿ��� �߰� �ɼ� ������ �Է� �� �����մϴ�.             = */
    /* = -------------------------------------------------------------------------- = */

    /* PayPlus���� ���̴� �ſ�ī��� ���� �Ķ���� �Դϴ�
    �� �ش� ī�带 ����â���� ������ �ʰ� �Ͽ� ���� �ش� ī��� ������ �� ������ �մϴ�. (ī��� �ڵ�� �Ŵ����� ����)
    <input type="hidden" name="not_used_card" value="CCPH:CCSS:CCKE:CCHM:CCSH:CCLO:CCLG:CCJB:CCHN:CCCH"> */

    /* �ſ�ī�� ������ OKĳ���� ���� ���θ� ���� â�� �����ϴ� �Ķ���� �Դϴ�
         OKĳ���� ����Ʈ �������� ��쿡�� â�� �������ϴ�
        <input type="hidden" name="save_ocb"        value="Y"> */

    /* ���� �Һ� ���� �� ����
           value���� "7" �� �������� ��� => ī������� ����â�� �Һ� 7������ ���ð���
    <input type="hidden" name="fix_inst"        value="07"> */

    /*  ������ �ɼ�
            �� �����Һ�    (������ ������ �������� ���� �� ������ ������ ������)                             - "" �� ����
            �� �Ϲ��Һ�    (KCP �̺�Ʈ �̿ܿ� ���� �� ��� ������ ������ �����Ѵ�)                           - "N" �� ����
            �� ������ �Һ� (������ ������ �������� ���� �� ������ �̺�Ʈ �� ���ϴ� ������ ������ �����Ѵ�)   - "Y" �� ����
    <input type="hidden" name="kcp_noint"       value=""> */


    /*  ������ ����
            �� ���� 1 : �Һδ� �����ݾ��� 50,000 �� �̻��� ��쿡�� ����
            �� ���� 2 : ������ �������� ������ �ɼ��� Y�� ��쿡�� ���� â�� ����
            ��) �� ī�� 2,3,6���� ������(����,��,����,�Ｚ,����,����,�Ե�,��ȯ) : ALL-02:03:04
            BC 2,3,6����, ���� 3,6����, �Ｚ 6,9���� ������ : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
    <input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"> */

    /* ���ī�� ���� ���� �Ķ���� �Դϴ�.(���հ���â ���� ����)
    <input type="hidden" name="used_card_YN"        value="Y">
    /* ���ī�� ���� �Ķ���� �Դϴ�. (�ش� ī�常 ����â�� ���̰� �����ϴ� �Ķ�����Դϴ�. used_card_YN ���� Y�϶� ����˴ϴ�.
    /<input type="hidden" name="used_card"        value="CCBC:CCKM:CCSS">

    /* �ؿ�ī�� �����ϴ� �Ķ���� �Դϴ�.(�ؿܺ���, �ؿܸ�����, �ؿ�JCB�� �����Ͽ� ǥ��)
    <input type="hidden" name="used_card_CCXX"        value="Y">

    /*  ������� ���� ���� �Ķ����
         �� �ش� ������ ����â���� ���̰� �մϴ�.(�����ڵ�� �Ŵ����� ����) */
?>

<input type="hidden" name="kcp_noint"       value="">
<input type="hidden" name="wish_vbank_list" value="">
<?php


    /*  ������� �Ա� ���� �����ϴ� �Ķ���� - �߱��� + 3��
    <input type="hidden" name="vcnt_expire_term" value="3"> */


    /*  ������� �Ա� �ð� �����ϴ� �Ķ����
         HHMMSS�������� �Է��Ͻñ� �ٶ��ϴ�
         ������ ���Ͻô°�� �⺻������ 23��59��59�ʰ� ������ �˴ϴ�
         <input type="hidden" name="vcnt_expire_term_time" value="120000"> */


    /* ����Ʈ ������ ���� ����(�ſ�ī��+����Ʈ) ���θ� ������ �� �ֽ��ϴ�.- N �ϰ�� ���հ��� ������
        <input type="hidden" name="complex_pnt_yn" value="N">    */


    /* ��ȭ��ǰ�� ������ ������ �� ���̵� ������ �ؾ� �մϴ�.(�ʼ� ����)
        <input type="hidden" name="tk_shop_id" value="">    */


    /* ���ݿ����� ��� â�� ��� ���θ� �����ϴ� �Ķ���� �Դϴ�
         �� Y : ���ݿ����� ��� â ���
         �� N : ���ݿ����� ��� â ��� ����
         �� ���� : ���ݿ����� ��� �� KCP ���������� ���������� ���ݿ����� ��� ���Ǹ� �ϼž� �մϴ� */
?>
    <input type="hidden" name="disp_tax_yn"     value="N">
<?php
    /* ����â�� ������ ����Ʈ�� �ΰ� �÷����� ���� ��ܿ� ����ϴ� �Ķ���� �Դϴ�
       ��ü�� �ΰ� �ִ� URL�� ��Ȯ�� �Է��ϼž� �ϸ�, �ִ� 150 X 50  �̸� ũ�� ����

    �� ���� : �ΰ� �뷮�� 150 X 50 �̻��� ��� site_name ���� ǥ�õ˴ϴ�. */
?>
    <input type="hidden" name="site_logo"       value="">
<?php
    /* ����â ���� ǥ�� �Ķ���� �Դϴ�. ������ �⺻���� ����Ͻ÷��� Y�� �����Ͻñ� �ٶ��ϴ�
        2010-06�� ���� �ſ�ī��� ������¸� �����˴ϴ�
        <input type="hidden" name="eng_flag"      value="Y"> */
?>

<?php
     /* skin_indx ���� ��Ų�� ������ �� �ִ� �Ķ�����̸� �� 7������ �����˴ϴ�.
        ������ ���Ͻø� 1���� 7���� ���� �־��ֽñ� �ٶ��ϴ�. */
?>
    <input type="hidden" name="skin_indx"      value="1">

<?php
    /* ��ǰ�ڵ� ���� �Ķ���� �Դϴ�.(��ǰ���� ���� �����Ͽ� ó���� �� �ִ� �ɼǱ���Դϴ�.)
    <input type="hidden" name="good_cd"      value=""> */

    /* = -------------------------------------------------------------------------- = */
    /* =   4. �ɼ� ���� END                                                         = */
    /* ============================================================================== */
?>
<p align="center"><input type="image" src="<?php echo $g4['shop_img_path']; ?>/btn_settle.gif" border="0"  onclick="return jsf__pay(this.form);" /></p>
</form>