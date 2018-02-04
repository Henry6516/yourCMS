<?php if (!defined('THINK_PATH')) exit();?><a class="navbar-brand"  href="<?php echo U('Saler/Saler/show_saler_page');?>" >返回条件选择页
    <!--onClick="javascript :history.back(-1);"-->
</a>

<br>
<br>
<br>
<br>
<br>
<br>

<form action='/Demo/Operate/upload' method='post' enctype='multipart/form-data'>
    <table>
        <tr>
            <td>销售运营杂费:</td>
            <td><input name='file' type="file"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input type='submit' value='Submit'/></td>
        </tr>
    </table>
</form>
<a href="../../../../Y_saleOpeFee.xlsx">导出销售运营杂费模板</a>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>


<form action='/Demo/Operate/upload_dev' method='post' enctype='multipart/form-data'>
    <table>
        <tr>
            <td>开发运营杂费:</td>
            <td><input name='file' type="file"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input type='submit' value='Submit'/></td>
        </tr>
    </table>
</form>
<a href="../../../../Y_devOperateFee.xlsx">导出开发运营杂费模板</a>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<form action='/Demo/Operate/upload_possessMan' method='post' enctype='multipart/form-data'>
    <table>
        <tr>
            <td>美工运营杂费:</td>
            <td><input name='file' type="file"/></td>
        </tr>
       <tr>
            <td></td>
            <td><input type='submit' value='Submit'/></td>
        </tr>
    </table>
</form>
<a href="../../../../Y_PossessOperateFee.xlsx">导出美工运营杂费模板</a>


<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<form action='/Demo/Operate/upload_purchase' method='post' enctype='multipart/form-data'>
    <table>
        <tr>
            <td>采购运营杂费:</td>
            <td><input name='file' type="file"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input type='submit' value='Submit'/></td>
        </tr>
    </table>
</form>
<a href="../../../../Y_purOperateFee.xlsx">导出采购运营杂费模板</a>