/** global: Craft */
/** global: Garnish */

Craft.HubSpotObjectsActions = Garnish.Base.extend(
    {
        $actionBtn: null,
        $actionMenu: null,
        $spinner: null,

        _initialized: false,

        init: function (settings) {
            this.setSettings(settings, Craft.HubSpotObjectsActions.defaults);
            this.initActions();

            this._initialized = true;
        },

        initActions: function () {
            var safeMenuActions = [],
                destructiveMenuActions = [];

            var i;

            for (i = 0; i < this.settings.actions.length; i++) {
                var action = this.settings.actions[i];

                if (!action.destructive) {
                    safeMenuActions.push(action);
                } else {
                    destructiveMenuActions.push(action);
                }
            }

            if (safeMenuActions.length || destructiveMenuActions.length) {
                var $menu = this.getActionMenu();

                var $safeList = this._createMenuTriggerList(safeMenuActions, false),
                    $destructiveList = this._createMenuTriggerList(destructiveMenuActions, true);

                if ($safeList) {
                    $safeList.appendTo($menu);
                }

                if ($safeList && $destructiveList) {
                    $('<hr/>').appendTo($menu);
                }

                if ($destructiveList) {
                    $destructiveList.appendTo($menu);
                }
            }

            if (this.$actionBtn) {
                this.$actionBtn.menubtn();
                this.$actionBtn.data('menubtn').on('optionSelect', $.proxy(this, '_handleMenuActionTriggerSubmit'));
            }
        },

        getActionMenu: function () {
            if (this.$actionMenu) {
                return this.$actionMenu;
            }

            this.$actionBtn = $('<div class="btn menubtn dashed" title="' + Craft.t('app', 'Actions') + '"/>')
                .insertAfter(this.$addBtn);

            this.$actionMenu = $('<ul class="menu"/>')
                .insertAfter(this.$actionBtn);

            return this.$actionMenu;
        },

        activateSpinner: function () {
            this.$spinner.removeClass('hidden');
        },

        deactivateSpinner: function () {
            this.$spinner.addClass('hidden');
        },

        updateBtn: function () {
            this.updateActionBtn();
        },

        updateActionBtn: function () {
            if (this.$objects.length === 0) {
                this.enableActionBtn();
            } else {
                this.disableActionBtn();
            }
        },

        disableActionBtn: function () {
            if (this.$actionBtn && !this.$actionBtn.hasClass('disabled')) {
                this.$actionBtn.addClass('disabled');

                if (this._initialized) {
                    this.$actionBtn.velocity('fadeOut', Craft.HubSpotObjectsField.ADD_FX_DURATION);
                } else {
                    this.$actionBtn.hide();
                }
            }
        },

        enableActionBtn: function () {
            if (this.$actionBtn && this.$actionBtn.hasClass('disabled')) {
                this.$actionBtn.removeClass('disabled');

                if (this._initialized) {
                    this.$actionBtn.velocity('fadeIn', Craft.HubSpotObjectsField.REMOVE_FX_DURATION);
                } else {
                    this.$actionBtn.show();
                }
            }
        },

        _createMenuTriggerList: function (actions, destructive) {
            if (actions && actions.length) {
                var $ul = $('<ul/>');

                for (var i = 0; i < actions.length; i++) {
                    var action = actions[i];

                    if (action.trigger) {
                        $(action.trigger).appendTo($ul);
                    } else {
                        var actionClass = action.type;
                        $('<li/>').append($('<a/>', {
                            id: Craft.formatInputId(actionClass) + '-actiontrigger',
                            'class': (destructive ? 'error' : null),
                            'data-action': actionClass,
                            text: actions[i].name
                        })).appendTo($ul);
                    }
                }

                return $ul;
            }
        },

        _handleMenuActionTriggerSubmit: function (ev) {
            var $option = $(ev.option);

            if ($option.hasClass('disabled') || $option.data('ignore') || $option.data('custom-handler')) {
                return;
            }

            var actionClass = $option.data('action');
            this.submitAction(actionClass);
        },

        actionData: function (action, actionClass) {
            return $.extend(this.settings.data, this.settings.actionData, action.params, {
                action: actionClass
            });
        },

        submitAction: function (actionClass) {
            var action;

            for (var i = 0; i < this.settings.actions.length; i++) {
                if (this.settings.actions[i].type === actionClass) {
                    action = this.settings.actions[i];
                    break;
                }
            }

            if (!action || (action.confirm && !confirm(action.confirm))) {
                return;
            }

            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.actionAction,
                this.actionData(action, actionClass),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.afterAction(action, response);

                            if (response.message) {
                                Craft.cp.displayNotice(
                                    Craft.t('hubspot', response.message)
                                );
                            }
                        } else {
                            if (jqXHR.responseJSON.message) {
                                Craft.cp.displayError(
                                    Craft.t('hubspot', jqXHR.responseJSON.message)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },

        afterAction: function (action, response) {
            Craft.cp.runQueue();
            this.onAfterAction(action, response);
        },

        onAfterAction: function (action, response) {
            this.settings.onAfterAction(action, response);
            this.trigger('afterAction', {action: action, response: response});
        }
    },
    {
        ADD_FX_DURATION: 200,
        REMOVE_FX_DURATION: 200,

        defaults: {
            actions: {},
            actionData: {},
            actionAction: 'hubspot/cp/fields/perform-action',
            onAfterAction: $.noop
        }
    }
);
Craft.HubSpotObjectsField = Craft.HubSpotObjectsActions.extend(
    {
        objectSelect: null,
        objectSort: null,

        $container: null,
        $objectsContainer: null,
        $objects: null,
        $addBtn: null,

        $spinner: null,

        _$triggers: null,

        init: function (container, settings) {
            this.$container = $(container);
            this.$container.data('objects', this);

            this.setSettings(settings, $.extend(Craft.HubSpotObjectsActions.defaults, Craft.HubSpotObjectsField.defaults));

            // No reason for this to be sortable if we're only allowing 1 selection
            if (this.settings.limit === 1) {
                this.settings.sortable = false;
            }

            this.$objectsContainer = this.$container.children('.objects');
            ;
            this.$addBtn = this.$container.find('.btn.add');
            if (this.$addBtn) {
                this.addListener(this.$addBtn, 'activate', 'onAdd');
            }

            if (this.$addBtn && this.settings.limit === 1) {
                this.$addBtn
                    .css('position', 'absolute')
                    .css('top', 0)
                    .css(Craft.left, 0);
            }

            this.$spinner = $('<div class="spinner hidden"/>').appendTo(this.$container);

            this.initSObjectSort();
            this.initActions();
            this.resetObjects();

            this._initialized = true;
        },

        initSObjectSort: function () {
            if (this.settings.sortable) {
                this.objectSort = new Garnish.DragSort({
                    container: this.$objectsContainer,
                    ignoreHandleSelector: '.ignore-sort',
                    axis: 'list',
                    collapseDraggees: true,
                    magnetStrength: 4,
                    helperLagBase: 1.5
                });
            }
        },

        getObjects: function () {
            return this.$objectsContainer.children();
        },

        resetObjects: function () {
            if (this.$objects !== null) {
                this.removeObjects(this.$objects);
            } else {
                this.$objects = $();
            }
            this.addObjects(this.getObjects());
        },

        addObjects: function ($objects) {
            if (this.settings.sortable) {
                this.objectSort.addItems($objects);
            }

            var that = this;
            $objects.each(function (index, el) {
                that.createRow(el);
            });

            this.$objects = this.$objects.add($objects);
            this.updateBtn();
        },

        createRow: function (container) {
            return new Craft.HubSpotObjectItem(container, $.extend({}, {
                onRemove: $.proxy(function (item) {
                    this.removeObjects(item.$container);
                    item.$container.remove();
                }, this)
            }, this.settings.itemSettings));
        },

        removeObjects: function ($objects) {
            // Disable the hidden input in case the form is submitted before this element gets removed from the DOM
            $objects.children('input').prop('disabled', true);

            this.$objects = this.$objects.not($objects);
            this.updateBtn();

            this.onRemoveObjects();
        },

        updateBtn: function () {
            this.base();
            this.updateAddBtn();
        },

        updateAddBtn: function () {
            if (this.canAddMore()) {
                this.enableAddBtn();
            } else {
                this.disableAddBtn();
            }
        },

        onAdd: function () {
            if (!this.canAddMore()) {
                return;
            }

            this.addRow();
        },

        addRow: function (id) {
            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.createRowAction,
                this.itemData({id: id}),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            var $object = $(response.html);

                            Craft.appendHeadHtml(response.headHtml);
                            Craft.appendFootHtml(response.footHtml);

                            this.appendSObject($object);
                            this.addObjects($object);

                            return $object;
                        }
                    },
                    this
                )
            );
        },

        appendSObject: function ($object) {
            $object.appendTo(this.$objectsContainer);
        },

        itemData: function (data) {
            return $.extend(data, this.settings.itemData);
        },

        canAddMore: function () {
            return (!this.settings.limit || this.$objects.length < this.settings.limit);
        },

        disableAddBtn: function () {
            if (this.$addBtn && !this.$addBtn.hasClass('disabled')) {
                this.$addBtn.addClass('disabled');

                if (this.settings.limit === 1) {
                    if (this._initialized) {
                        this.$addBtn.velocity('fadeOut', Craft.HubSpotObjectsField.ADD_FX_DURATION);
                    } else {
                        this.$addBtn.hide();
                    }
                }
            }
        },

        enableAddBtn: function () {
            if (this.$addBtn && this.$addBtn.hasClass('disabled')) {
                this.$addBtn.removeClass('disabled');

                if (this.settings.limit === 1) {
                    if (this._initialized) {
                        this.$addBtn.velocity('fadeIn', Craft.HubSpotObjectsField.REMOVE_FX_DURATION);
                    } else {
                        this.$addBtn.show();
                    }
                }
            }
        },

        onRemoveObjects: function () {
            this.trigger('removeObjects');
            this.settings.onRemoveObjects();
        },

        afterAction: function (action, response) {
            this.addRow(response.id);
            this.base(action, response);
        }
    },
    {
        ADD_FX_DURATION: 200,
        REMOVE_FX_DURATION: 200,

        defaults: {
            limit: null,
            sortable: true,

            actionData: {},
            actionAction: 'hubspot/cp/fields/perform-action',

            createRowAction: 'hubspot/cp/fields/create-item',
            itemData: {},
            itemSettings: {},

            onRemoveObjects: $.noop,
            onSortChange: $.noop,
            onAfterAction: $.noop
        }
    }
);

Craft.HubSpotObjectItem = Craft.HubSpotObjectsActions.extend(
    {
        $container: null,

        $actionContainer: null,
        $actionButton: null,
        $actionTriggers: null,

        $associateBtn: null,
        $dissociateBtn: null,
        $toggleBtn: null,

        $objectInput: null,
        $objectLabel: null,

        id: null,

        init: function (container, settings) {
            this.setSettings(settings, $.extend(Craft.HubSpotObjectsActions.defaults, Craft.HubSpotObjectItem.defaults));

            this.$container = $(container);
            this.$container.data('item', this);

            this.$associateBtn = this.$container.find('.associate');
            if (this.$associateBtn) {
                this.addListener(this.$associateBtn, 'activate', 'onAssociate');
            }

            this.$dissociateBtn = this.$container.find('.remove');
            if (this.$dissociateBtn) {
                this.addListener(this.$dissociateBtn, 'activate', 'onDissociate');
            }

            this.$toggleBtn = this.$container.find('.toggle-edit');
            if (this.$toggleBtn) {
                this.addListener(this.$toggleBtn, 'activate', 'onToggle');
            }

            this.$actionContainer = this.$container.find('.actions');
            this.$actionButton = this.$actionContainer.find('.menubtn');
            this.$actionTriggers = this.$actionContainer.find('.triggers');


            this.$actionBtn = this.$actionContainer.find('.menubtn');
            this.$actionMenu = this.$actionContainer.find('.triggers');

            this.$objectLabel = this.$container.find('.objectIdLabel');
            this.$objectInput = this.$container.find('input.objectId');
            this.id = this.$objectInput.val();

            this.$spinner = $('<div class="spinner hidden"/>').appendTo(this.$container);

            this.initActions();

            Craft.initUiElements(this.$container);
        },

        actionData: function (action, actionClass) {
            return $.extend(this.base(action, actionClass), {id: this.id});
        },

        onToggle: function (ev) {
            if (!this.id) {
                this.remove();
                return;
            }

            this.updateSObjectId(this.id);
            this.toggleEdit();
        },

        toggleEdit: function () {
            this.$container.toggleClass('edit-mode');
        },

        checkButtonVisibility: function () {
            if (this.$objectInput.val() !== '') {
                this.$objectLabel.removeClass('hidden')
            } else {
                this.$objectLabel.addClass('hidden')
            }
        },

        updateSObjectId: function (value) {
            this.$objectInput.val(value);
            this.$objectLabel.html(value);

            this.id = value;

            this.checkButtonVisibility();
        },

        getSortOrder: function () {
            var sortOrder = this.$container.parent('.objects').children().index(this.$container);
            return sortOrder + 1;
        },

        associationData: function () {
            var data = $.extend({}, this.settings.data, this.settings.associationData);

            data['objectId'] = this.id;
            data['newObjectId'] = this.$objectInput.val();
            data['sortOrder'] = this.getSortOrder();
            return data;
        },

        onAssociate: function (ev) {
            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.associateAction,
                this.associationData(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.toggleEdit();

                            if (response.hasOwnProperty("objectId")) {
                                this.updateSObjectId(response.objectId);
                            }

                            if (this.settings.associationMessageSuccess) {
                                Craft.cp.displayNotice(
                                    Craft.t('hubspot', this.settings.associationMessageSuccess)
                                );
                            }
                        } else {
                            if (this.settings.associationMessageError) {
                                Craft.cp.displayError(
                                    Craft.t('hubspot', this.settings.associationMessageError)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },

        dissociationData: function () {
            var data = $.extend({}, this.settings.data, this.settings.associationData);
            data['objectId'] = this.id;
            return data;
        },

        onDissociate: function (ev) {
            if (!this.id) {
                this.remove();
                return;
            }

            this.activateSpinner();

            Craft.actionRequest(
                'POST',
                this.settings.dissociateAction,
                this.dissociationData(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.deactivateSpinner();

                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.remove();

                            if (this.settings.dissociationMessageSuccess) {
                                Craft.cp.displayNotice(
                                    Craft.t('hubspot', this.settings.dissociationMessageSuccess)
                                );
                            }
                        } else {
                            if (this.settings.dissociationMessageError) {
                                Craft.cp.displayError(
                                    Craft.t('hubspot', this.settings.dissociationMessageError)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },

        remove: function () {
            this.destroy();

            this.trigger('remove', {item: this});
            this.settings.onRemove(this);
        }
    },
    {
        defaults: {
            actionAction: 'hubspot/cp/fields/perform-item-action',

            data: {},
            associationData: {},
            associateAction: 'hubspot/cp/objects/associate',

            dissociateData: {},
            dissociateAction: 'hubspot/cp/objects/dissociate',

            associationMessageError: "Failed to associate HubSpot Object.",
            associationMessageSuccess: "Successfully associated HubSpot Object.",

            dissociationMessageError: "Failed to dissociate HubSpot Object.",
            dissociationMessageSuccess: "Successfully dissociated HubSpot Object.",

            onRemove: $.noop
        }
    }
);