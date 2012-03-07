<?php

class ParserImgController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionView()
	{
        Item::model()->UploadPhoto();
		$this->render('view');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/

    public function actionDownloadItem()
    {
        ob_start();
        $param = Yii::app()->session['param'];
        Yii::import('application.vendors.*');
        require_once('Zend/Dom/Query.php');

        $link_param = array(
            '0' => 'http://www.aliexpress.com/fm-store/403546/search/',
        );

        $link = array();
        $mas2 = array();

        foreach ($link_param as $link_p) {
            $page = array('0' => '1');
            $obj = CurlAuth::init();
            $result = $obj->load($link_p . "1.html")->content;
            //echo $result;

            $dom = new Zend_Dom_Query($result);
            $result_page = $dom->query('//#pagination-bottom//div.pos-right//a');

            foreach ($result_page as $p)
            {
                if (is_numeric($p->nodeValue))
                    $page[] = $p->nodeValue;
            }
            $first = array_shift($page);
            $end = array_pop($page);
            $pages = range($first, $end);
            unset($page);

            ////////////////////////////////////
            foreach ($pages as $p_n) {
                if ($p_n > 1) break;
                $result = $obj->load($link_p . $p_n . ".html")->content;
                $dom = new Zend_Dom_Query($result);
                $items = $dom->query('//#list-items//li.list-item//h2//a');

                foreach ($items as $item)
                {
                    $it = $item->getAttribute('href');
                    $link = $it;

                    $item_count = Yii::app()->db->createCommand()
                            ->select('COUNT(*) as items')
                            ->from('item')
                            ->where('link=:l', array(':l' => $link))
                            ->queryRow();
                    if ($item_count['items'] > 0) //если количесвто больше нуля то переходим к следующему элементу
                    {
                        echo "<li>Данные актуальны</li>";
                        continue;
                    }

                    $result_item = $obj->load($link)->content;
                    $dom_item = new Zend_Dom_Query($result_item);
                    $title = $dom_item->query('//h1#product-name');
                    foreach ($title as $t) {
                        $title_item = trim(strip_tags($t->nodeValue));
                    }
                    $prices = $dom_item->query('//div.price');
                    foreach ($prices as $p) {
                        $price_item = preg_replace("/(\s){2,}/", ' ', trim(strip_tags($p->nodeValue)));
                    }
                    $pieces = $dom_item->query('//span.unit-disc');
                    foreach ($pieces as $pcs) {
                        $pcs_item = preg_replace("/(\s){2,}/", ' ', trim(strip_tags($pcs->nodeValue)));
                    }

                    $connection = Yii::app()->db;
                    $connection->active = true;
                    $sql = "INSERT INTO item (title, price, link, pcs, store_link, date_add)
    	        	VALUES('" . $title_item . "', '" . $price_item . "', '" . $link . "', '" . $pcs_item . "', '" . $link_p . "', '" . date('Y-m-d H:i:s') . "');";
                    $connection->createCommand($sql)->execute();
                    $last_id = $connection->getLastInsertID();
                    $connection->active = false;

                    $item_imgs = $dom_item->query('//#custom-description//img');

                    $connection2 = Yii::app()->db;
                    $connection2->active = true;
                    $image_link = "INSERT INTO gallery (id_item, link) VALUES ";
                    foreach ($item_imgs as $img) {
                        $noemptyImg = $img->getAttribute('src');
                        if ($noemptyImg != '' && stripos($noemptyImg, 'i.aliimg.com')) {
                            if ((stripos($noemptyImg, '414696971_793.jpg') == FALSE) &&
                                (stripos($noemptyImg, '414696969_274.jpg') == FALSE) &&
                                (stripos($noemptyImg, '414696970_010.jpg') == FALSE)
                            )
                                $image_link .= "('" . $last_id . "', '" . $img->getAttribute('src') . "'),";
                        }
                        sleep(2);
                        set_time_limit(0);
                    }
                    $image_link = substr($image_link, 0, -1);
                    $connection2->createCommand($image_link)->execute();
                    $connection2->active = false;
                    unset($image_link);
                    echo "<li>".$p_n." <a href='".$link."'>".$link."</a></li>";
                    sleep(2);
                    ob_flush();
                    flush();
                }

            }
            /////////////////////////////////////////
            //CVarDumper::dump($pages, 10, true);

        }
    }
}