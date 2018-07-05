# php-qrcode-function

php-qrcode官网地址：http://phpqrcode.sourceforge.net/
使用的GD库qrcode包生成二维码

注意：.PHP环境必须开启支持GD2扩展库支持（一般情况下都是开启状态）

本次使用的是thinkphp3.2.3,具体实现代码都写在了function文件中

在index控制器中调用：
    如：

    <?php

    	class IndexController extends Controller {

    		public function index(){

    			$arr = array(
    				'url'    => 'https://www.baidu.com',
    				'qrDir'  => '/Public/qrcode',
    				'qrName' => '123',
    				'logo'   => '',
    				'top'    => '免费WIFI上万',
    				'botton' => '微信',
    			);
    			qrcode($arr);
                
			}
		}




说明：

方法解读：
下载下来的类文件是一个压缩包，里边包含很多文件和演示程序，我们只需要里边的phpqrcode.php这一个文件就可以生成二维码了。它是一个多个类的集合文件，我们需要用到里边的QRcode类（第2963行）的png()方法（第3090行：


第1个参数$text：二维码包含的内容，可以是链接、文字、json字符串等等；

第2个参数$outfile：默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径；

第3个参数$level：默认为L，这个参数可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)，这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别；

第4个参数$size：控制生成图片的大小，默认为4；

第5个参数$margin：控制生成二维码的空白区域大小；

第6个参数$saveandprint：保存二维码图片并显示出来，$outfile必须传递图片路径；
