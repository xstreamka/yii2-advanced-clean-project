<?php

namespace frontend\components;

use frontend\helpers\ImageHelper;
use yii\helpers\Html;
use yii\widgets\LinkPager;

class Pager extends LinkPager
{

	public $get;

	/**
	 * Renders the page buttons.
	 * @return string the rendering result
	 */
	protected function renderPageButtons()
	{
		if ($this->nextPageLabel === false) {
			$this->prevPageLabel = '<span aria-hidden="true">' . ImageHelper::show_svg('arrow-right', 'svg-arrow-right arrow-rotate-180 svg-icon_red') . '</span><span class="sr-only">Previous</span>';
		}
		if ($this->nextPageLabel === false) {
			$this->nextPageLabel = '<span aria-hidden="true">' . ImageHelper::show_svg('arrow-right', 'svg-arrow-right svg-icon_red') . '</span><span class="sr-only">Next</span>';
		}
		$pageCount = $this->pagination->getPageCount();
		if ($pageCount < 2 && $this->hideOnSinglePage) {
			return '';
		}

		$buttons = [];
		$currentPage = $this->pagination->getPage();

		// first page
		$firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
		if ($firstPageLabel !== false) {
			$buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
		}

		list($beginPage, $endPage) = $this->getPageRange();

		// prev page
		if ($this->prevPageLabel !== false && $currentPage != $beginPage) {
			if (($page = $currentPage - 1) < 0) {
				$page = 0;
			}
			$buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
		}

		// internal pages
		for ($i = $beginPage; $i <= $endPage; ++$i) {
			$buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i == $currentPage);
		}

		// next page
		if ($this->nextPageLabel !== false && ($currentPage != $endPage)) {
			if (($page = $currentPage + 1) >= $pageCount - 1) {
				$page = $pageCount - 1;
			}
			$buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
		}

		// last page
		$lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
		if ($lastPageLabel !== false) {
			$buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
		}

		$buttons = array_map(function ($item) {
			return str_replace('%2F','/',$item);
		}, $buttons);

		return Html::tag('ul', implode("\n", $buttons), $this->options);
	}

	/**
	 * Renders a page button.
	 * You may override this method to customize the generation of page buttons.
	 * @param string $label the text label for the button
	 * @param integer $page the page number
	 * @param string $class the CSS class for the page button.
	 * @param boolean $disabled whether this page button is disabled
	 * @param boolean $active whether this page button is active
	 * @return string the rendering result
	 */
	protected function renderPageButton($label, $page, $class, $disabled, $active)
	{
		$options = ['class' => empty($class) ? $this->pageCssClass : $class];
		if ($active) {
			Html::addCssClass($options, $this->activePageCssClass);
		}
		if ($disabled) {
			Html::addCssClass($options, $this->disabledPageCssClass);

			return Html::tag('li', Html::tag('span', $label), $options);
		}
		$linkOptions = $this->linkOptions;
		$linkOptions['data-page'] = $page;

		$this->pagination->params = $this->get;

		return Html::tag('li', Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
	}

}