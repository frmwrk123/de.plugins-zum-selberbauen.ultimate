{capture assign='pageTitle'}{lang}wcf.acp.ultimate.category.list{/lang}{/capture}
{include file='header'}

<script data-relocate="true" type="text/javascript">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.category'] = { };
		actionObjects['de.plugins-zum-selberbauen.ultimate.category']['delete'] = new ULTIMATE.Action.Delete('ultimate\\data\\category\\CategoryAction', '.jsCategoryRow');
		
		WCF.Clipboard.init('ultimate\\acp\\page\\UltimateCategoryListPage', {@$hasMarkedItems}, actionObjects);
		
		var options = { };
		options.emptyMessage = '{lang}wcf.acp.ultimate.category.noContents{/lang}';
		{if $pages > 1}
			options.refreshPage = true;
		{/if}
		
		new WCF.Table.EmptyTableHandler($('#categoryTableContainer'), 'jsCategoryRow', options);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.ultimate.category.list{/lang}</h1>
</header>

{assign var=encodedURL value=$url|rawurlencode}
{assign var=encodedAction value=$action|rawurlencode}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='ultimate' controller='UltimateCategoryList' link="pageNo=%d&action=$encodedAction&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canManageCategories')}
				<li><a href="{link application='ultimate' controller='UltimateCategoryAdd'}{/link}" title="{lang}wcf.acp.ultimate.category.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.category.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
{hascontent}
<div id="categoryTableContainer" class="tabularBox tabularBoxTitle marginTop">
	<header>
		<h2>{lang}wcf.acp.ultimate.category.list{/lang} <span class="counter badge badgeInverse" title="{lang}wcf.acp.ultimate.category.list.count{/lang}">{#$items}</span></h2>
	</header>
	<table class="table jsClipboardContainer" data-type="de.plugins-zum-selberbauen.ultimate.category">
		<thead>
			<tr>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID{if $sortField == 'categoryID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='ultimate' controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryID&sortOrder={if $sortField == 'categoryID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle{if $sortField == 'categoryTitle'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryTitle&sortOrder={if $sortField == 'categoryTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.title{/lang}</a></th>
				<th class="columnDescription">{lang}wcf.acp.ultimate.category.description{/lang}</th>
				<th class="columnSlug{if $sortField == 'categorySlug'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categorySlug&sortOrder={if $sortField == 'categorySlug' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.slug{/lang}</a></th>
				<th class="columnDigits columnContents{if $sortField == 'categoryContents'} active {@$sortOrder}{/if}"><a href="{link application='ultimate' controller='UltimateCategoryList'}action={@$encodedAction}&pageNo={@$pageNo}&sortField=categoryContents&sortOrder={if $sortField == 'categoryContents' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.ultimate.category.contents{/lang}</a></th>
				
				{event name='headColumns'}
			</tr>
		</thead>
		
		<tbody>
			{content}
				{foreach from=$objects item=category}
					<tr id="categoryContainer{@$category->categoryID}" class="jsCategoryRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$category->categoryID}" /></td>
						<td class="columnIcon">
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageCategories')}
								<a href="{link application='ultimate' controller='UltimateCategoryEdit' id=$category->categoryID}{/link}"><span title="{lang}wcf.acp.ultimate.category.edit{/lang}" class="icon icon16 icon-pencil jsTooltip"></span></a>
							{else}
								<span title="{lang}wcf.acp.ultimate.category.edit{/lang}" class="icon icon16 icon-pencil disabled"></span>
							{/if}
							
							{if $__wcf->session->getPermission('admin.content.ultimate.canManageCategories') && $category->categoryID > 2}
								<span title="{lang}wcf.acp.ultimate.category.delete{/lang}" class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" data-object-id="{@$category->categoryID}" data-confirm-message="{lang}wcf.acp.ultimate.category.delete.sure{/lang}"></span>
							{else}
								<span title="{lang}wcf.acp.ultimate.category.delete{/lang}" class="icon icon16 icon-remove disabled"></span>
							{/if}
							
							{event name='buttons'}
						</td>
						<td class="columnID"><p>{@$category->categoryID}</p></td>
						<td class="columnTitle"><p>{if $__wcf->session->getPermission('admin.content.ultimate.canManageCategories')}<a title="{lang}wcf.acp.ultimate.category.edit{/lang}" href="{link application='ultimate' controller='UltimateCategoryEdit' id=$category->categoryID}{/link}">{lang}{@$category->categoryTitle}{/lang}</a>{else}{lang}{@$category->categoryTitle}{/lang}{/if}</p></td>
						<td class="columnDescription"><p>{lang}{@$category->categoryDescription}{/lang}</p></td>
						<td class="columnSlug"><p>{@$category->categorySlug}</p></td>
						<td class="columnContents"><p><a title="{lang}wcf.acp.ultimate.category.showContents{/lang}" href="{link application='ultimate' controller='UltimateContentList'}categoryID={@$category->categoryID}{/link}">{$category->getContentsLazy()|count}</a></p></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			{/content}
		</tbody>
	</table>
</div>
{hascontentelse}
<p class="info">{lang}wcf.acp.ultimate.category.noContents{/lang}</p>
{/hascontent}
<div class="contentNavigation">
	{@$pagesLinks}
	
	<div class="clipboardEditor jsClipboardEditor" data-types="[ 'de.plugins-zum-selberbauen.ultimate.category' ]"></div>
		
	<nav>
		<ul>
			{if $__wcf->session->getPermission('admin.content.ultimate.canManageCategoriesy')}
				<li><a href="{link application='ultimate' controller='UltimateCategoryAdd'}{/link}" title="{lang}wcf.acp.ultimate.category.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.acp.ultimate.category.add{/lang}</span></a></li>
			{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>
</div>

{include file='footer'}
 
