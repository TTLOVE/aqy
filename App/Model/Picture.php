<?php

namespace Model;

/**
 *  生成图片模块
 */
class Picture
{
    /**
        * 生产图片
        *
        * @param $posterId 海报主键id
        * @param $templateId 模板id
        * @param $title 文案内容
        *
        * @return string
     */
    function createPic($posterId, $templateId, $title='我内牛满面你却春风得意') {
        // 获取对应模板信息
        $source = HOST . '/static/template/' . $templateId . ".jpg";
        $main = imagecreatefromjpeg( $source );
        $width = imagesx($main);
        $height = imagesy($main);

        // 生成背景
        $target = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $white);

        // 放置模板
        imagecopyresampled($target, $main, 0, 0, 0, 0, $width, $height, $width, $height);

        // 处理文案数据
        $target = $this->dealWithWords($target, $width, $title);
        $setPath = '/Pic/poster_' . $posterId . '_' . $templateId . '.jpg';
		$filePath = PUBLIC_PATH . $setPath;
		//imagepng($backImg, $filename);
		imagejpeg($target, $filePath);
		$url = HOST . $setPath;
		return $url;
    }

    /**
        * 生成文字图片
        *
        * @param $target 原图片数据
        * @param $width 长度
        * @param $title 标题
        *
        * @return 图片数据
     */
    private function dealWithWords($target, $width, $title)
    {
        // 设置字体和大小
        $font = PUBLIC_PATH . "/static/template/ttf/fangzheng.ttf";
        $fontSize = 43;//像素字体

        // 放置文字
        $fontColor = imagecolorallocate($target, 0, 0, 0);//字的RGB颜色
        // 查看数据长度
        $wordsCount = mb_strlen($title, 'UTF8');
        $perWord = 7;
        $pageCount = $wordsCount%$perWord ? intval($wordsCount/$perWord)+1 : intval($wordsCount/$perWord);
        $goCount = $pageCount>4 ? 4 : $pageCount;
        switch ($goCount) {
            case 1:
                $basePosition = 700;
                $perPosition = 0;
                break;
            
            case 2:
                $basePosition = 650;
                $perPosition = 140;
                break;
            
            case 3:
                $basePosition = 620;
                $perPosition = 100;
                break;
            
            case 4:
                $basePosition = 590;
                $perPosition = 80;
                break;
            
            default:
                
                break;
        }
        for ($i = 0; $i < $goCount; $i++) {
            $begin = $i * $perWord;
            $word = mb_substr($title, $begin, $perWord, 'utf-8');
            $position = $basePosition + $perPosition * $i;
            $fontBox = imagettfbbox($fontSize, 0, $font, $word);//文字水平居中实质
            imagettftext($target, $fontSize, 0, ceil(($width - $fontBox[2]) / 2), $position, $fontColor, $font, $word);
        }
        return $target;
    }

	/**
	 * 设置模板为33的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate33($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 350, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template33.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 295, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0, 0, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 350, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为32的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate32($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template32.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0, 0, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 865, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为31的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate31($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 200, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template31.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 255, 255, 255);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 870, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为30的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate30($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 200, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template30.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0, 0, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 865, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}


	/**
	 * 设置模板为29的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate29($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 260, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template29.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 255, 170, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 255, 170, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 865, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为28的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate28($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 300, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template28.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 255, 209, 29);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 225, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 75, 0, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 285, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为27的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate27($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template27.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 141, 0, 7);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 865, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为26的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate26($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 300, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template26.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 80, 25, 6);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 240, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 80, 25, 6);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 290, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为25的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate25($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 260, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template25.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, 20, 200, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 255, 255, 255);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, 20, 260, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为24的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate24($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template24.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 778, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 97, 1, 129);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 850, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为23的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate23($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template23.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 141, 0, 7);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 870, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为17的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate17($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template17.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 102, 26, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格颜色朱红
		$moneyColor = imagecolorallocate($backImg, 102, 26, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为18的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate18($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template18.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色朱红
		$titleColor = imagecolorallocate($backImg, 140, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 785, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格朱红
		$moneyColor = imagecolorallocate($backImg, 140, 0, 0);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 840, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为19的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate19($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template19.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色黑色
		$titleColor = imagecolorallocate($backImg, 9, 30, 61);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 795, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格红色
		$moneyColor = imagecolorallocate($backImg, 171, 30, 41);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 845, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为20的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate20($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 300, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template20.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色红色
		$titleColor = imagecolorallocate($backImg, 248, 45, 39);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格红色
		$moneyColor = imagecolorallocate($backImg, 248, 45, 39);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 855, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为21的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate21($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 100, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template21.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色红色
		$titleColor = imagecolorallocate($backImg, 211, 18, 25);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 55, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黄色
		$moneyColor = imagecolorallocate($backImg, 255, 203, 27);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 95, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为22的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate22($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template22.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色白色
		$titleColor = imagecolorallocate($backImg, 253, 241, 215);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 825, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白色
		$moneyColor = imagecolorallocate($backImg, 253, 241, 215);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 870, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为13的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate13($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template13.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色白色
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黄色
		$moneyColor = imagecolorallocate($backImg, 255, 255, 38);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为14的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate14($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template14.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 114, 114, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色黑色
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黑色
		$moneyColor = imagecolorallocate($backImg, 255, 255, 255);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为15的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate15($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template15.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 115, 115, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色白色
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 810, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黄色
		$moneyColor = imagecolorallocate($backImg, 255, 255, 38);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为16的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate16($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template16.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 116, 116, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色黑色
		$titleColor = imagecolorallocate($backImg, 255, 237, 83);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 800, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黑色
		$moneyColor = imagecolorallocate($backImg, 255, 237, 83);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 850, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为12的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate12($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template12.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色黑色
		$titleColor = imagecolorallocate($backImg, 51, 51, 51);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 665, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黑色
		$moneyColor = imagecolorallocate($backImg, 51, 51, 51);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 790, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为11的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate11($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$newWidth = 600;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 250, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template11.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色白色
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 800, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格粉色
		$moneyColor = imagecolorallocate($backImg, 255, 23, 116);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 870, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为10的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate10($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$newWidth = 600;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template10.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色白色
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 780, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黄色
		$moneyColor = imagecolorallocate($backImg, 255, 255, 77);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 840, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为9的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate9($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 300, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template9.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色白色
		$titleColor = imagecolorallocate($backImg, 255, 255, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = 40;
		imagefttext($backImg, 29, 0, $padding, 345, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品底图黄色
		$moneyLength = strlen($goodsMoney);
		$backLenth = 80 * ($moneyLength + 3);
		$backbgImg = imagecreate($backLenth, 45);
		$yellowBG = imagecolorallocate($backbgImg, 255, 219, 101);
		imagefill($backbgImg, 0, 0, $yellowBG);
		imagecopyresized($backImg, $backbgImg, 40, 370, 0, 0, $backLenth, 48, $backLenth, 48);

		//商品价格红色
		$moneyColor = imagecolorallocate($backImg, 195, 0, 24);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyPoints = 52;
		imagefttext($backImg, 24, 0, $moneyPoints, 405, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为8的商品海报
	 *
	 * @param $storeinfo  商家信息
	 * @param $goodsinfo  　商品信息
	 * @param $goodsimage 　商品图片
	 * @param $codeimg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate8($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 300, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template8.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑色
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名称颜色蓝色
		$titleColor = imagecolorallocate($backImg, 67, 227, 255);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 255, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黄色
		$moneyColor = imagecolorallocate($backImg, 253, 248, 36);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 315, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为7的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate7($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 0, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template7.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名黑色
		$titleColor = imagecolorallocate($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 785, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为6的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate6($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 200, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template6.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 800, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格黄名字
		$moneyColor = imagecolorallocate($backImg, 255, 255, 77);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为5的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate5($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 200, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template5.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 800, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为4的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate4($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 200, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template4.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名白字
		$titleColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 780, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	/**
	 * 设置模板为3的商品海报
	 *
	 * @param $storeInfo  商家信息
	 * @param $goodsInfo  　商品信息
	 * @param $goodsImage 　商品图片
	 * @param $codeImg    　二维码图片
	 * @param $num        　数量
	 * @param $length     　长度
	 *
	 * @return
	 */
	public static function setTemplate3($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(600, 1067);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 600 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 600 ) {
			$rate = 600 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 600;
		} else {
			$newWidth = 600;
			$newHeight = $rate * $goodsImgHeight;
		}
		imagecopyresized($backImg, $goodsImg, 0, 200, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//放置模板
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/template3.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 0, 0, 0, 0, 600, 1067, $boxWidth, $boxHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 448, 928, 0, 0, 112, 112, $codeWidth, $codeHeight);

		//贴头像
		if ( $storeInfo['store_logo'] == UPYUN_URL . '/upload/loadingsquare.gif' ) {
			$storeInfo['store_logo'] = SITE_URL . '/Public/Poster/images/logo_new.jpg';
		}
		$logoImgs = file_get_contents($storeInfo['store_logo']);
		if ( $logoImgs ) {
			$logoImg = imagecreatefromstring($logoImgs);
		} else {
			$logoImg = imagecreatefromstring(file_get_contents(SITE_URL . '/Public/Poster/images/logo_new.jpg'));
		}
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 20, 926, 0, 0, 96, 96, $logoWidth, $logoHeight);

		//商家名黑字
		$storeColor = imagecolorallocate($backImg, 0, 0, 0);
		$storeNameNum = 8;
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$storeName = mb_substr($storeInfo['store_name'], 0, $storeNameNum, 'utf-8');
		imagefttext($backImg, 18, 0, 145, 950, $storeColor, $simHeiPath, $storeName);

		//商品名朱红
		$titleColor = imagecolorallocate($backImg, 145, 39, 30);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$num = $num > 15 ? 15 : $num;
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (30 - $length) * 20 / 2;
		imagefttext($backImg, 29, 0, $padding, 785, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//商品价格白名字
		$moneyColor = imagecolorallocate($backImg, 0xFF, 0xFF, 0xFF);
		$goodsMoney = $goodsInfo['price'] . "元";
		$moneyLength = strlen($goodsMoney);
		$padding1 = (30 - $moneyLength) * 20 / 2;
		$moneyPoints = $padding1;
		imagefttext($backImg, 30, 0, $moneyPoints, 860, $moneyColor, $ttfPath, $goodsMoney);

		return $backImg;
	}

	public static function drawTypeThree($storeInfo, $goodsInfo, $goodsImage, $codeImg, $num, $length)
	{
		//背景图
		$backImg = imagecreatetruecolor(580, 800);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($backImg, 0, 0, $grey);

		//商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 540 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 540 ) {
			$rate = 540 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 540;
		} else {
			$newWidth = 540;
			$newHeight = $rate * $goodsImgHeight;
		}
		//echo "($goodsImgWidth,$goodsImgHeight) => ($newWidth, $newHeight)";
		imagecopyresized($backImg, $goodsImg, 20, 90, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		$bottomImg = imagecreatetruecolor(580, 270);
		$grey = imagecolorallocate($backImg, 238, 238, 238);
		imagefill($bottomImg, 0, 0, $grey);
		imagecopyresized($backImg, $bottomImg, 0, 630, 0, 0, 580, 270, 580, 270);
		//白色背景图
		$whiteImg = imagecreatetruecolor(480, 200);
		$white = imagecolorallocate($whiteImg, 255, 255, 255);
		imagefill($whiteImg, 0, 0, $white);
		imagecopyresized($backImg, $whiteImg, 50, 550, 0, 0, 480, 200, 480, 200);

		//贴头像
		//裁圆形
		$logoPath = ATTACHMENT_PATH . '/upload/storelogo_' . $store_id . '.png';
		$cutter = new ImageCutter($storeInfo['store_logo'], $logoPath);
		$logoImg = $cutter->getCutted();
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 260, 520, 0, 0, 60, 60, $logoWidth, $logoHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 360, 588, 0, 0, 150, 150, $codeWidth, $codeHeight);

		//考拉LOGO
		//$rightImg = imagecreatefromstring(file_get_contents("http://tb.lifeq.com.cn/Public/image/poster/logo_s.png"));
		$imagePath = realpath(__DIR__ . '/../../../Public/image');
		$rightImg = imagecreatefromstring(file_get_contents($imagePath . '/leftlogo.png'));
		$rightWidth = imagesx($rightImg);
		$rightHeight = imagesy($rightImg);
		imagecopyresized($backImg, $rightImg, 70, 655, 0, 0, 220, 81, $rightWidth, $rightHeight);
		//
		//商品名称框框
		$boxImg = imagecreatefromstring(file_get_contents(SITE_URL . "/Public/css/poster/images/tem3_goodnamebox.png"));
		$boxWidth = imagesx($boxImg);
		$boxHeight = imagesy($boxImg);
		imagecopyresized($backImg, $boxImg, 80, 10, 0, 0, 400, 70, $boxWidth, $boxHeight);

		//商品名
		//$black = imagecolorallocate($backImg, 0x00, 0x00, 0x00);
		$titleColor = imagecolorallocate($backImg, 0x6E, 0x5D, 0x45);
		//$black = imagecolorexact($backImg, 0, 0, 0);
		$simHeiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		$goodsInfo['goods_name'] = mb_substr($goodsInfo['goods_name'], 0, $num, 'utf-8');
		//为了居中计算x坐标
		$padding = (18 - $length) * 20 / 2;
		//var_dump($length);
		//var_dump($padding);
		$xpoint = 100 + $padding;
		//var_dump(mb_strlen($goodsInfo['goods_name']));
		//imagefttext($backImg, 20, 0, 0, 0, $black, $ttfPath, $goodsInfo['goods_name']);
		//imagefttext($backImg, 30, 0, 100, 60, $black, $simHeiPath, $goodsInfo['goods_name']);
		imagefttext($backImg, 30, 0, $xpoint, 60, $titleColor, $simHeiPath, $goodsInfo['goods_name']);

		//$red = imagecolorexact($backImg, 0xF1, 0x5B, 0x5C);
		$red = imagecolorallocate($backImg, 0xF1, 0x5B, 0x5C);
		imagefttext($backImg, 20, 0, 70, 640, $red, $ttfPath, '￥');
		imagefttext($backImg, 30, 0, 100, 640, $red, $ttfPath, $goodsInfo['price']);

		//$grey = imagecolorexact($backImg, 0x99, 0x99, 0x99);
		//$grey = imagecolorallocate($backImg, 0x99, 0x99, 0x99);
		//imagefttext($backImg, 14, 0, 70, 730, $grey, $ttfPath, '考拉先生微信支付官方服务商');

		return $backImg;
	}

	public static function drawTypeTwo($storeInfo, $goodsInfo, $goodsImage, $codeImg)
	{
		//白色背景图
		$backImg = imagecreatetruecolor(580, 900);
		$white = imagecolorallocate($backImg, 255, 255, 255);
		imagefill($backImg, 0, 0, $white);
		//贴商品图片
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 570 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 570 ) {
			$rate = 570 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 570;
		} else {
			$newWidth = 570;
			$newHeight = $rate * $goodsImgHeight;
		}
		//echo "($goodsImgWidth,$goodsImgHeight) => ($newWidth, $newHeight)";
		imagecopyresized($backImg, $goodsImg, 10, 90, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//贴模板
		$middlePath = SITE_URL . '/Public/css/poster/images/tem2_pho_bg.png';
		$middleImg = imagecreatefromstring(file_get_contents($middlePath));
		$middleWidth = imagesx($middleImg);
		$middleHeight = imagesy($middleImg);
		imagecopyresized($backImg, $middleImg, 0, 0, 0, 0, 580, 900, $middleWidth, $middleHeight);
		//贴头像
		//裁圆形
		$logoPath = ATTACHMENT_PATH . '/upload/storelogo_' . $store_id . '.png';
		$cutter = new ImageCutter($storeInfo['store_logo'], $logoPath);
		$logoImg = $cutter->getCutted();
		$logoWidth = imagesx($logoImg);
		$logoHeight = imagesy($logoImg);
		imagecopyresized($backImg, $logoImg, 40, 40, 0, 0, 60, 60, $logoWidth, $logoHeight);

		//二维码
		$codeWidth = imagesx($codeImg);
		$codeHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 30, 730, 0, 0, 130, 130, $codeWidth, $codeHeight);

		//考拉LOGO
		//$rightImg = imagecreatefromstring(file_get_contents("http://tb.lifeq.com.cn/Public/image/poster/logo_s.png"));
		$imagePath = realpath(__DIR__ . '/../../../Public/image');
		$rightImg = imagecreatefromstring(file_get_contents($imagePath . '/logo.png'));
		$rightWidth = imagesx($rightImg);
		$rightHeight = imagesy($rightImg);
		imagecopyresized($backImg, $rightImg, 330, 772, 0, 0, 230, 85, $rightWidth, $rightHeight);

		//商品名
		//$black = imagecolorallocate($backImg, 0x00, 0x00, 0x00);
		$titleColor = imagecolorallocate($backImg, 0x6E, 0x5D, 0x45);
		//$black = imagecolorexact($backImg, 0, 0, 0);
		$simheiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		//imagefttext($backImg, 20, 0, 0, 0, $black, $ttfPath, $goodsInfo['goods_name']);
		imagefttext($backImg, 30, 0, 140, 80, $titleColor, $simheiPath, $goodsInfo['goods_name']);

		//$red = imagecolorexact($backImg, 0xF1, 0x5B, 0x5C);
		$red = imagecolorallocate($backImg, 0xF1, 0x5B, 0x5C);
		imagefttext($backImg, 30, 0, 30, 698, $red, $ttfPath, '￥');
		imagefttext($backImg, 40, 0, 66, 698, $red, $ttfPath, $goodsInfo['price']);

		//$grey = imagecolorexact($backImg, 0x99, 0x99, 0x99);
		//$grey = imagecolorallocate($backImg, 0x99, 0x99, 0x99);
		//imagefttext($backImg, 16, 0, 280, 850, $grey, $ttfPath, '考拉先生微信支付官方服务商');

		return $backImg;
	}

	public static function drawTypeOne($storeInfo, $goodsInfo, $goodsImage, $codeImg)
	{
		$temUrl = SITE_URL . "/Public/image/poster/pho_bg3.png";
		$backUrl = SITE_URL . "/Public/image/poster/background.jpg";
		$rightUrl = SITE_URL . "/Public/image/poster/logo_s.png";

		$temImg = imagecreatefromstring(file_get_contents($temUrl));
		$backImg = imagecreatefromstring(file_get_contents($backUrl));
		//$logoImg = $cutter->getCutted();
		$logoImg = imagecreatefromstring(file_get_contents($storeInfo['store_logo']));
		$goodsImg = imagecreatefromstring(file_get_contents($goodsImage));

		//商品图片
		$goodsImgWidth = imagesx($goodsImg);
		$goodsImgHeight = imagesy($goodsImg);
		$rate = 610 / $goodsImgWidth;
		if ( $rate * $goodsImgHeight < 610 ) {
			$rate = 610 / $goodsImgHeight;
			$newWidth = $rate * $goodsImgWidth;
			$newHeight = 610;
		} else {
			$newWidth = 610;
			$newHeight = $rate * $goodsImgHeight;
		}
		//echo "($goodsImgWidth,$goodsImgHeight) => ($newWidth, $newHeight)";
		imagecopyresized($backImg, $goodsImg, 20, 20, 0, 0, $newWidth, $newHeight, $goodsImgWidth, $goodsImgHeight);

		//模板
		$temImgWidth = imagesx($temImg);
		$temImgHeight = imagesy($temImg);
		imagecopyresized($backImg, $temImg, 0, 0, 0, 0, 650, 1000, $temImgWidth, $temImgHeight);

		//头像
		$whiteImg = imagecreatetruecolor(115, 115);
		$white = imagecolorallocate($whiteImg, 255, 255, 255);
		imagefill($whiteImg, 0, 0, $white);
		$logoImgWidth = imagesx($logoImg);
		$logoImgHeight = imagesy($logoImg);
		imagecopyresized($backImg, $whiteImg, 40, 450, 0, 0, 115, 115, 115, 115);
		imagecopyresized($backImg, $logoImg, 47, 457, 0, 0, 100, 100, $logoImgWidth, $logoImgHeight);


		//二维码
		$codeImgWidth = imagesx($codeImg);
		$codeImgHeight = imagesy($codeImg);
		imagecopyresized($backImg, $codeImg, 40, 780, 0, 0, 186, 186, $codeImgWidth, $codeImgHeight);

		//右下角图片
		//$rightImg = imagecreatefromstring(file_get_contents($rightUrl));
		$imagePath = realpath(__DIR__ . '/../../../Public/image');
		$rightImg = imagecreatefromstring(file_get_contents($imagePath . '/logo.png'));
		$rightImgWidth = imagesx($rightImg);
		$rightImgHeight = imagesy($rightImg);
		imagecopyresized($backImg, $rightImg, 400, 875, 0, 0, 230, 85, $rightImgWidth, $rightImgHeight);

		//$black = imagecolorallocate($backImg, 0x00, 0x00, 0x00);
		$titleColor = imagecolorallocate($backImg, 0x6E, 0x5D, 0x45);
		//$black = imagecolorexact($backImg, 0, 0, 0);
		$simheiPath = realpath(__DIR__ . '/../../../Attachment/ttf/simhei.ttf');
		$ttfPath = realpath(__DIR__ . '/../../../Attachment/ttf/fangzheng.ttf');
		//imagefttext($backImg, 20, 0, 0, 0, $black, $ttfPath, $goodsInfo['goods_name']);
		imagefttext($backImg, 42, 0, 40, 670, $titleColor, $simheiPath, $goodsInfo['goods_name']);

		//$red = imagecolorexact($backImg, 0xF1, 0x5B, 0x5C);
		$red = imagecolorallocate($backImg, 0xF1, 0x5B, 0x5C);
		imagefttext($backImg, 30, 0, 40, 750, $red, $ttfPath, '￥');
		imagefttext($backImg, 42, 0, 85, 750, $red, $ttfPath, $goodsInfo['price']);

		//$grey = imagecolorexact($backImg, 0x99, 0x99, 0x99);
		//$grey = imagecolorallocate($backImg, 0x99, 0x99, 0x99);
		//imagefttext($backImg, 20, 0, 280, 956, $grey, $ttfPath, '考拉先生微信支付官方服务商');

		return $backImg;
	}

	public static function checkPoster($store_id, $goods_id)
	{
		$attachmentPath = realpath(__DIR__ . '/../../../Attachment');
		$filename = $attachmentPath . '/upload/poster_' . $store_id . '_' . $goods_id . '.png';
		if ( file_exists($filename) ) {
			return SITE_URL . "Attachment/upload/poster_" . $store_id . '_' . $goods_id . '.png';
		} else {
			return false;
		}
	}
}
