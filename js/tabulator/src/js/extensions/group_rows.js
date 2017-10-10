

//public group object
var GroupComponent = function (group){
	this.group = group;
	this.type = "GroupComponent";
}

GroupComponent.prototype.getKey = function(){
	return this.group.key;
};

GroupComponent.prototype.getElement = function(){
	return this.group.element;
};

GroupComponent.prototype.getRows = function(){
	var output = []

	this.group.rows.forEach(function(row){
		output.push(row.getComponent());
	});

	return output;
};

GroupComponent.prototype.getSubGroups = function(){
	var output = [];

	this.group.groupList.forEach(function(child){
		output.push(child.getComponent());
	});

	return output;
};

GroupComponent.prototype.getParentGroup = function(){
	return this.group.parent ? this.group.parent.getComponent() : false;
};

GroupComponent.prototype.getVisibility = function(){
	return this.group.visible;
};

GroupComponent.prototype.show = function(){
	this.group.show()
};

GroupComponent.prototype.hide = function(){
	this.group.hide();
};

GroupComponent.prototype.toggle = function(){
	this.group.toggleVisibility();
};

GroupComponent.prototype._getSelf = function(){
	return this.group;
};

//////////////////////////////////////////////////
//////////////// Group Functions /////////////////
//////////////////////////////////////////////////

var Group = function(groupManager, parent, level, key, generator, oldGroup){

	this.groupManager = groupManager;
	this.parent = parent;
	this.key = key;
	this.level = level;
	this.hasSubGroups = level < (groupManager.groupIDLookups.length - 1);
	this.addRow = this.hasSubGroups ? this._addRowToGroup : this._addRow;
	this.type = "group"; //type of element
	this.old = oldGroup;
	this.rows = [];
	this.groups = [];
	this.groupList = [];
	this.generator = generator;
	this.element = $("<div class='tabulator-row tabulator-group tabulator-group-level-" + level + "' role='rowgroup'></div>");
	this.arrowElement = $("<div class='tabulator-arrow'></div>");
	this.height = 0;
	this.outerHeight = 0;
	this.initialized = false;
	this.calcs = {};
	this.initialized = false;

	this.visible = oldGroup ? oldGroup.visible : (typeof groupManager.startOpen[level] !== "undefined" ? groupManager.startOpen[level] : groupManager.startOpen[0]);

	this.addBindings();
};

Group.prototype.addBindings = function(){
	var self = this,
	dblTap,	tapHold, tap, toggleElement;

	if(self.groupManager.table.options.groupToggleElement){
		toggleElement = self.groupManager.table.options.groupToggleElement == "arrow" ? self.arrowElement : self.element;

		toggleElement.on("click", function(e){
			e.stopPropagation();
			e.stopImmediatePropagation();
			self.toggleVisibility();
		});
	}


	//handle group click events
	if (self.groupManager.table.options.groupClick){
		self.element.on("click", function(e){
			self.groupManager.table.options.groupClick(e, self.getComponent());
		})
	}

	if (self.groupManager.table.options.groupDblClick){
		self.element.on("dblclick", function(e){
			self.groupManager.table.options.groupDblClick(e, self.getComponent());
		})
	}

	if (self.groupManager.table.options.groupContext){
		self.element.on("contextmenu", function(e){
			self.groupManager.table.options.groupContext(e, self.getComponent());
		})
	}

	if (self.groupManager.table.options.groupTap){

		tap = false;

		self.element.on("touchstart", function(e){
			tap = true;
		});

		self.element.on("touchend", function(e){
			if(tap){
				self.groupManager.table.options.groupTap(e, self.getComponent());
			}

			tap = false;
		});
	}

	if (self.groupManager.table.options.groupDblTap){

		dblTap = null;

		self.element.on("touchend", function(e){

			if(dblTap){
				clearTimeout(dblTap);
				dblTap = null;

				self.groupManager.table.options.groupDblTap(e, self.getComponent());
			}else{

				dblTap = setTimeout(function(){
					clearTimeout(dblTap);
					dblTap = null;
				}, 300);
			}

		});
	}


	if (self.groupManager.table.options.groupTapHold){

		tapHold = null;

		self.element.on("touchstart", function(e){
			clearTimeout(tapHold);

			tapHold = setTimeout(function(){
				clearTimeout(tapHold);
				tapHold = null;
				tap = false;
				self.groupManager.table.options.groupTapHold(e, self.getComponent());
			}, 1000)

		});

		self.element.on("touchend", function(e){
			clearTimeout(tapHold);
			tapHold = null;
		});
	}

};

Group.prototype._addRowToGroup = function(row){

	var level = this.level + 1;

	if(this.hasSubGroups){
		var groupID = this.groupManager.groupIDLookups[level](row.getData());

		if(!this.groups[groupID]){
			var group = new Group(this.groupManager, this, level, groupID, this.groupManager.headerGenerator[level] || this.groupManager.headerGenerator[0], this.old ? this.old.groups[groupID] : false);

			this.groups[groupID] = group;
			this.groupList.push(group);
		}

		this.groups[groupID].addRow(row);
	}

};

Group.prototype._addRow = function(row){
	this.rows.push(row);
};

Group.prototype.getHeadersAndRows = function(){
	var output = [];

	output.push(this);

	this._visSet();

	if(this.visible){

		if(this.groupList.length){
			this.groupList.forEach(function(group){
				output = output.concat(group.getHeadersAndRows());
			});

		}else{
			if(this.groupManager.table.extExists("columnCalcs") && this.groupManager.table.extensions.columnCalcs.hasTopCalcs()){
				this.calcs.top = this.groupManager.table.extensions.columnCalcs.generateTopRow(this.rows);
				output.push(this.calcs.top);
			}

			output = output.concat(this.rows);

			if(this.groupManager.table.extExists("columnCalcs") && this.groupManager.table.extensions.columnCalcs.hasBottomCalcs()){
				this.calcs.bottom = this.groupManager.table.extensions.columnCalcs.generateBottomRow(this.rows);
				output.push(this.calcs.bottom);
			}
		}
	}else{
		if(this.groupManager.table.options.groupClosedShowCalcs){
			if(this.groupManager.table.extExists("columnCalcs")){
				if(this.groupManager.table.extensions.columnCalcs.hasTopCalcs()){
					this.calcs.top = this.groupManager.table.extensions.columnCalcs.generateTopRow(this.rows)
					output.push(this.calcs.top);
				}

				if(this.groupManager.table.extensions.columnCalcs.hasBottomCalcs()){
					this.calcs.bottom = this.groupManager.table.extensions.columnCalcs.generateBottomRow(this.rows);
					output.push(this.calcs.bottom);
				}
			}
		}
	}

	return output;
};

Group.prototype.getRows = function(){
	this._visSet();

	return this.visible ? this.rows : [];
};

Group.prototype.getRowCount = function(){
	var count = 0;

	if(this.groupList.length){
		this.groupList.forEach(function(group){
			count += group.getRowCount();
		});
	}else{
		count = this.rows.length;
	}
	return count;
};

Group.prototype.toggleVisibility = function(){
	if(this.visible){
		this.hide();
	}else{
		this.show();
	}
};

Group.prototype.hide = function(){
	this.visible = false;

	if(this.groupManager.table.rowManager.getRenderMode() == "classic"){

		this.element.removeClass("tabulator-group-visible");

		this.rows.forEach(function(row){
			row.getElement().detach();
		});
	}else{
		this.groupManager.updateGroupRows(true);
	}

	this.groupManager.table.options.groupVisibilityChanged(this.getComponent(), false);
};

Group.prototype.show = function(){
	var self = this;

	self.visible = true;

	if(this.groupManager.table.rowManager.getRenderMode() == "classic"){

		this.element.addClass("tabulator-group-visible");

		self.rows.forEach(function(row){
			self.getElement().after(row.getElement());
			row.initialize();
		});
	}else{
		this.groupManager.updateGroupRows(true);
	}

	this.groupManager.table.options.groupVisibilityChanged(this.getComponent(), true);
};

Group.prototype._visSet = function(){
	var data = [];

	if(typeof this.visible == "function"){

		this.rows.forEach(function(row){
			data.push(row.getData());
		});

		this.visible = this.visible(this.key, this.getRowCount(), data, this.getRowCount());
	}
};

Group.prototype.getRowGroup = function(row){
	var match = false;
	if(this.groupList.length){
		this.groupList.forEach(function(group){
			var result = group.getRowGroup(row);

			if(result){
				match = result;
			}
		});
	}else{
		if(this.rows.find(function(item){
			return item === row;
		})){
			match = this;
		}
	}

	return match;
};

////////////// Standard Row Functions //////////////

Group.prototype.getElement = function(){
	this.addBindingsd = false;

	this._visSet();

	var data = [];

	this.rows.forEach(function(row){
		data.push(row.getData());
	});

	if(this.visible){
		this.element.addClass("tabulator-group-visible");
	}else{
		this.element.removeClass("tabulator-group-visible");
	}

	this.element.children().detach();

	this.element.html(this.generator(this.key, this.getRowCount(), data, this.getComponent())).prepend(this.arrowElement);

	// this.addBindings();

	return this.element;
};

//normalize the height of elements in the row
Group.prototype.normalizeHeight = function(){
	this.setHeight(this.element.innerHeight())
};

Group.prototype.initialize = function(force){
	if(!this.initialized || force){
		this.normalizeHeight();
		this.initialized = true;
	}
};

Group.prototype.reinitialize = function(){
	this.initialized = false;
	this.height = 0;

	if(this.element.is(":visible")){
		this.initialize(true);
	}
};

Group.prototype.setHeight = function(height){
	if(this.height != height){
		this.height = height;
		this.outerHeight = this.element.outerHeight();
	}
};

//return rows outer height
Group.prototype.getHeight = function(){
	return this.outerHeight;
};

Group.prototype.reinitializeHeight = function(){
};
Group.prototype.calcHeight = function(){
};
Group.prototype.setCellHeight = function(){
};
Group.prototype.clearCellHeight = function(){
}


//////////////// Object Generation /////////////////
Group.prototype.getComponent = function(){
	return new GroupComponent(this);
};

//////////////////////////////////////////////////
////////////// Group Row Extension ///////////////
//////////////////////////////////////////////////

var GroupRows = function(table){

	this.table = table; //hold Tabulator object

	this.groupIDLookups = false; //enable table grouping and set field to group by
	this.startOpen = [function(){return false;}]; //starting state of group
	this.headerGenerator = [function(){return "";}];
	this.groupList = []; //ordered list of groups
	this.groups = {}; //hold row groups
};


//initialize group configuration
GroupRows.prototype.initialize = function(){
	var self = this,
	groupBy = self.table.options.groupBy,
	startOpen = self.table.options.groupStartOpen,
	groupHeader = self.table.options.groupHeader;

	self.headerGenerator = [function(){return "";}];
	this.startOpen = [function(){return false;}]; //starting state of group

	self.table.extensions.localize.bind("groups.item", function(langValue, lang){
		self.headerGenerator[0] = function(value, count, data){ //header layout function
			return value + "<span>(" + count + " " + ((count === 1) ? langValue : lang.groups.items) + ")</span>";
		};
	});

	this.groupIDLookups = [];


	if(Array.isArray(groupBy) || groupBy){
		if(this.table.extExists("columnCalcs")){
			this.table.extensions.columnCalcs.removeCalcs();
		}
	}else{
		if(this.table.extExists("columnCalcs")){

			var cols = this.table.columnManager.getRealColumns();

			cols.forEach(function(col){
				if(col.definition.topCalc){
					self.table.extensions.columnCalcs.initializeTopRow();
				}

				if(col.definition.bottomCalc){
					self.table.extensions.columnCalcs.initializeBottomRow();
				}
			})
		}
	}



	if(!Array.isArray(groupBy)){
		groupBy = [groupBy];
	}

	groupBy.forEach(function(group){
		var lookupFunc, column;

		if(typeof group == "function"){
			lookupFunc = group;
		}else{
			column = self.table.columnManager.getColumnByField(group);

			if(column){
				lookupFunc = function(data){
					return column.getFieldValue(data);
				}
			}else{
				lookupFunc = function(data){
					return data[group];
				}
			}
		}

		self.groupIDLookups.push(lookupFunc);
	});



	if(startOpen){

		if(!Array.isArray(startOpen)){
			startOpen = [startOpen];
		}

		startOpen.forEach(function(level){
			level = typeof level == "function" ? level : function(){return true;};
		});

		self.startOpen = startOpen;
	}

	if(groupHeader){
		self.headerGenerator = Array.isArray(groupHeader) ? groupHeader : [groupHeader];
	}

	this.initialized = true;

};

//return appropriate rows with group headers
GroupRows.prototype.getRows = function(rows){
	if(this.groupIDLookups.length){

		this.table.options.dataGrouping();

		this.generateGroups(rows);

		if(this.table.options.dataGrouped){
			this.table.options.dataGrouped(this.getGroups());
		};

		return this.updateGroupRows();

	}else{
		return rows.slice(0);
	}

};

GroupRows.prototype.getGroups = function(){
	var groupComponents = [];

	if(this.table.options.dataGrouped){
		this.groupList.forEach(function(group){
			groupComponents.push(group.getComponent());
		});
	}

	return groupComponents;
};

GroupRows.prototype.getRowGroup = function(row){
	var match = false;

	this.groupList.forEach(function(group){
		var result = group.getRowGroup(row);

		if(result){
			match = result;
		}
	});

	return match;
};

GroupRows.prototype.countGroups = function(){
	return this.groupList.length;
};

GroupRows.prototype.generateGroups = function(rows){
	var self = this,
	oldGroups = self.groups;

	self.groups = {};
	self.groupList =[];

	rows.forEach(function(row){

		var groupID = self.groupIDLookups[0](row.getData());

		if(!self.groups[groupID]){
			var group = new Group(self, false, 0, groupID, self.headerGenerator[0], oldGroups[groupID]);

			self.groups[groupID] = group;
			self.groupList.push(group);
		}

		self.groups[groupID].addRow(row);
	});


}


GroupRows.prototype.updateGroupRows = function(force){
	var self = this,
	output = [],
	oldRowCount;

	self.groupList.forEach(function(group){
		output = output.concat(group.getHeadersAndRows());
	});

	//force update of table display
	if(force){
		oldRowCount = self.table.rowManager.displayRowsCount;

		self.table.rowManager.setDisplayRows(output);
		self.table.rowManager._virtualRenderFill(Math.floor((self.table.rowManager.element.scrollTop() / self.table.rowManager.element[0].scrollHeight) * oldRowCount));
	}

	return output;
};

GroupRows.prototype.scrollHeaders = function(left){
	this.groupList.forEach(function(group){
		group.arrowElement.css("margin-left", left);
	});
};

Tabulator.registerExtension("groupRows", GroupRows);