{include file='header'}

<script type="text/javascript">
	/* <![CDATA[ */
	$(function() {
		WCF.TabMenu.init();
	});
	/* ]]> */
</script>

<header class="boxHeadline">
	<hgroup>
		<h1>{lang}wcf.acp.ultimate.widgetArea.{@$action}{/lang}</h1>
	</hgroup>
</header>

{if $errorField}
	<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.form.{@$action}.success{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='UltimateWidgetAreaList'}{/link}" title="{lang}wcf.acp.menu.link.ultimate.appearance.widgetArea.list{/lang}" class="button"><img src="{@$__wcf->getPath()}icon/list.svg" alt="" class="icon24" /> <span>{lang}wcf.acp.menu.link.ultimate.appearance.widgetArea.list{/lang}</span></a></li>
			
			{event name='largeButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='UltimateWidgetAreaAdd'}{/link}{else}{link controller='UltimateWidgetAreaEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.widgetArea.general{/lang}</legend>
			<dl{if $errorField == 'widgetAreaName'} class="wcf-formError"{/if}>
				<dt><label for="widgetAreaName">{lang}wcf.acp.ultimate.widgetArea.name{/lang}</label></dt>
				<dd>
					<input type="text" id="widgetAreaName" name="widgetAreaName" value="{$widgetAreaName}" class="long" required="required" placeholder="{lang}wcf.acp.ultimate.widgetArea.name.placeholder{/lang}" />
					{if $errorField == 'categoryTitle'}
						<small class="wcf-innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.ultimate.widgetArea.name.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>			
		</fieldset>
		<fieldset>
			<legend>{lang}wcf.acp.ultimate.widgetArea.items{/lang}</legend>
			<div id="widgetList" class="container containerPadding marginTop shadow{if $__wcf->session->getPermission('admin.content.ultimate.canEditWidget') && $widgetNodeList|count > 1} sortableListContainer{/if}">
				{if $action == 'edit'}
				<ol class="sortableList" data-object-id="0">
					{assign var=oldDepth value=0}
					{foreach from=$widgetNodeList item=widget}
						{section name=i loop=$oldDepth-$widgetNodeList->getDepth()}</ol></li>{/section}
						
						<li class="{if $__wcf->session->getPermission('admin.content.ultimate.canEditWidget') && $widgetNodeList|count > 1}sortableNode {/if}jsWidget" data-object-name="{@$widget->widgetName}" data-object-id="{@$widget->widgetID}"{* {if $collapsedWidgetIDs|is_array} data-is-open="{if $collapsedWidgetIDs[$widget->widgetID]|isset}0{else}1{/if}"{/if} *}>
							<span class="sortableNodeLabel">
								<span class="buttons">
									
									{if $__wcf->getSession()->getPermission('admin.content.ultimate.canDeleteWidget')}
										<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 jsDeleteButton jsTooltip" data-object-id="{@$widget->widgetID}" data-confirm-message="{lang}wcf.acp.ultimate.widget.sure{/lang}" />
									{else}
										<img src="{@$__wcf->getPath()}icon/delete.svg" alt="" title="{lang}wcf.global.button.delete{/lang}" class="icon16 disabled" />
									{/if}
									
									{if $__wcf->getSession()->getPermission('admin.content.ultimate.canEditWidget')}
										{* todo: toggle icons aren't clickable *}
										<img src="{@$__wcf->getPath()}icon/{if !$widget->isDisabled}enabled{else}disabled{/if}.svg" alt="" title="{lang}wcf.global.button.{if !$widget->isDisabled}disable{else}enable{/if}{/lang}" class="icon16 jsToggleButton jsTooltip" data-object-id="{@$widget->widgetID}" />
									{else}
										<img src="{@$__wcf->getPath()}icon/{if !$widget->isDisabled}enabled{else}disabled{/if}.svg" alt="" title="{lang}wcf.global.button.{if !$widget->isDisabled}enable{else}disable{/if}{/lang}" class="icon16 disabled" />
									{/if}
									
									{if $__wcf->getSession()->getPermission('admin.content.ultimate.canEditWidget')}
										<img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.global.button.edit{/lang}" class="icon16 jsEditButton jsTooltip" data-object-id="{@$widget->widgetID}" />
									{else}
										<img src="{@$__wcf->getPath()}icon/edit.svg" alt="" title="{lang}wcf.global.button.edit{/lang}" class="icon16 disabled" />
									{/if}
									
									{event name='buttons'}
								</span>
								
								<span class="title">
									{$widget}
								</span>
							</span>
							
							<ol class="sortableList" data-object-id="{@$widget->widgetID}">
						{if !$widgetNodeList->current()->hasChildren()}
							</ol></li>
						{/if}
						{assign var=oldDepth value=$widgetNodeList->getDepth()}
					{/foreach}
					{section name=i loop=$oldDepth}</ol></li>{/section}
				</ol>
				{else}
					<p>{lang}wcf.acp.ultimate.widgetArea.addWidgetAreaFirst{/lang}</p>
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canEditWidget')}
					<div class="formSubmit">
						<button class="button default{if $action == 'add' || $widgetNodeList|count == 0} disabled" disabled="disabled{/if}" data-type="submit">{lang}wcf.global.button.save{/lang}</button>
					</div>
				{/if}
			</div>
		</fieldset>
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SID_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		{if $widgetAreaID|isset}<input type="hidden" name="id" value="{@$widgetAreaID}" />{/if}
	</div>
</form>
<form method="post">
	<div id="widgetTypeSelectContainer" class="container containerPadding marginTop shadow{if $action == 'add'} disabled{/if}">
		<dl>
			<dt><label>{lang}wcf.acp.ultimate.widgetArea.widgetTypes{/lang}</label></dt>
			<dd>
				<select id="widgetTypeIDs" name="widgetTypeIDs"{if $action == 'add'} class="disabled" disabled="disabled"{/if}>
					<option value="0" label="{lang}wcf.acp.ultimate.widgetArea.widgetTypes.none{/lang}">{lang}wcf.acp.ultimate.widgetArea.widgetTypes.none{/lang}</option>
					{htmlOptions options=$widgetTypes}
				</select>
				<small>
					{lang}wcf.acp.ultimate.widgetArea.widgetTypes.description{/lang}
				</small>
			</dd>
		</dl>
		<div class="formSubmit">
			<button class="button default disabled" disabled="disabled" data-type="submit">{lang}wcf.acp.ultimate.widgetArea.addToWidgetArea{/lang}</button>
		</div>
	</div>
</form>
<script type="text/javascript">
	/* <![CDATA[ */
		$(function() {
			{if $action == 'edit'}
				{if $__wcf->session->getPermission('admin.content.ultimate.canDeleteWidget')}
					new WCF.Action.Delete('ultimate\\data\\widget\\WidgetAction', $('.jsWidget'));
				{/if}
				{if $__wcf->session->getPermission('admin.content.ultimate.canEditWidget')}
					new WCF.Action.Toggle('ultimate\\data\\widget\\WidgetAction', $('.jsWidget'));
					{if $widgetNodeList|count > 1}
						var sortableNodes = $('.sortableNode');
						sortableNodes.each(function(index, node) {
							$(node).wcfIdentify();
						});
					{/if}
					$('#widgetList').find('button[data-type="submit"]').click(function(event) {
						event.preventDefault();
						if ($('#widgetList').find('.jsWidget').length == 0) {
							event.stopImmediatePropagation();
						} else {
							event.stopPropagation();
						}
					});
					new WCF.Sortable.List('widgetList', 'ultimate\\data\\widget\\WidgetAction', 0, { }, false);
					ULTIMATE.Permission.addObject({
						'admin.content.ultimate.canEditWidget': {if $__wcf->session->getPermission('admin.content.ultimate.canEditWidget')}true{else}false{/if},
						'admin.content.ultimate.canDeleteWidget': {if $__wcf->session->getPermission('admin.content.ultimate.canDeleteWidget')}true{else}false{/if}
					});
					WCF.Icon.addObject({
						'wcf.icon.edit': '{icon size='S'}edit{/icon}',
						'wcf.icon.delete': '{icon size='S'}delete{/icon}',
						'wcf.icon.enabled': '{icon size='S'}enabled{/icon}',
						'wcf.icon.disabled': '{icon size='S'}disabled{/icon}'
					});
					new ULTIMATE.Widget.Edit($('.jsWidget'));
					new ULTIMATE.Widget.Transfer('widgetTypeSelectContainer', 'widgetList', 'ultimate\\data\\widget\\WidgetAction', 0);
				{/if}
			{/if}
		});
	/* ]]> */
</script>

{include file='footer'}