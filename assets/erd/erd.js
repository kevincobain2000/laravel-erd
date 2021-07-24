
/* eslint-disable */

var nodeDataArray = [];
var linkDataArray = [];
var nodeDataArray = [];
var linkDataArray = [];
function init() {
  var $ = go.GraphObject.make; // for conciseness in defining templates

  myDiagram =
      $(go.Diagram, "myDiagramDiv", // must name or refer to the DIV HTML element
          {
              allowDelete: false,
              allowCopy: false,
              layout: $(go.ForceDirectedLayout),
              "undoManager.isEnabled": true
          });

  var itemTempl =
      $(go.Panel, "Horizontal", // this Panel is a row in the containing Table
          new go.Binding("portId", "name"), // this Panel is a "port"
          {
              background: "transparent", // so this port's background can be picked by the mouse
              fromSpot: go.Spot.Right, // links only go from the right side to the left side
              toSpot: go.Spot.Left,
              // allow drawing links from or to this port:
              fromLinkable: false,
              toLinkable: false
          },
          $(go.Shape, {
                  desiredSize: new go.Size(15, 15),
                  strokeJoin: "round",
                  strokeWidth: 3,
                  stroke: null,
                  margin: 2,
                  // but disallow drawing links from or to this shape:
                  fromLinkable: false,
                  toLinkable: false
              },
              new go.Binding("figure", "figure"),
              new go.Binding("stroke", "color"),
              new go.Binding("fill", "color")),
          $(go.TextBlock, {
                  margin: new go.Margin(0, 5),
                  column: 1,
                  font: "13px sans-serif",
                  alignment: go.Spot.Left,
                  // and disallow drawing links from or to this text:
                  fromLinkable: false,
                  toLinkable: false
              },
              new go.Binding("text", "name")),
          $(go.TextBlock, {
                  margin: new go.Margin(0, 5),
                  column: 2,
                  font: "11px courier",
                  alignment: go.Spot.Left
              },
              new go.Binding("text", "info"))
      );

  // define the Node template, representing an entity
  myDiagram.nodeTemplate =
      $(go.Node, "Auto", // the whole node panel
          {
              selectionAdorned: true,
              resizable: true,
              layoutConditions: go.Part.LayoutStandard & ~go.Part.LayoutNodeSized,
              fromSpot: go.Spot.AllSides,
              toSpot: go.Spot.AllSides,
              isShadowed: true,
              shadowOffset: new go.Point(3, 3),
              shadowColor: "#C5C1AA"
          },
          new go.Binding("location", "location").makeTwoWay(),
          // whenever the PanelExpanderButton changes the visible property of the "LIST" panel,
          // clear out any desiredSize set by the ResizingTool.
          new go.Binding("desiredSize", "visible", function(v) {
              return new go.Size(NaN, NaN);
          }).ofObject("LIST"),
          // define the node's outer shape, which will surround the Table
          $(go.Shape, "RoundedRectangle", {
              fill: 'white',
              stroke: "#eeeeee",
              strokeWidth: 6
          }),
          $(go.Panel, "Table", {
                  margin: 8,
                  stretch: go.GraphObject.Fill
              },
              $(go.RowColumnDefinition, {
                  row: 0,
                  sizing: go.RowColumnDefinition.None
              }),

              // the table header
              $(go.TextBlock, {
                      row: 0,
                      alignment: go.Spot.Left,
                      margin: new go.Margin(0, 24, 0, 2), // leave room for Button
                      font: "bold 16px sans-serif"
                  },
                  new go.Binding("text", "key")),
              // the collapse/expand button
              $("PanelExpanderButton", "LIST", // the name of the element whose visibility this button toggles
                  {
                      row: 0,
                      alignment: go.Spot.TopRight
                  }),
              // the list of Panels, each showing an attribute
              $(go.Panel, "Vertical", {
                      name: "LIST",
                      row: 1,
                      padding: 3,
                      alignment: go.Spot.TopLeft,
                      defaultAlignment: go.Spot.Left,
                      stretch: go.GraphObject.Horizontal,
                      itemTemplate: itemTempl,
                  },
                  new go.Binding("itemArray", "schema"))
          ) // end Table Panel
      ); // end Node
  // define the Link template, representing a relationship
  myDiagram.linkTemplate =
      $(go.Link, // the whole link panel
          {
              selectionAdorned: true,
              layerName: "Foreground",
              reshapable: true,
              routing: go.Link.AvoidsNodes,
              corner: 5,
              curve: go.Link.Orthogonal,
              curviness: 0,
          },
          $(go.Shape, // the link shape
              {
                  stroke: "#303B45",
                  strokeWidth: 1.5,
              }),
          $(go.Shape, // the arrowhead
              {
                  toArrow: "Triangle",
                  fill: "#1967B3"
              }),
          $(go.TextBlock, // the "from" label
              {
                  textAlign: "center",
                  font: "bold 12px sans-serif",
                  stroke: "#1967B3",
                  segmentIndex: 1.5,
                  segmentOffset: new go.Point(NaN, NaN),
                  segmentOrientation: go.Link.Horizontal,
                  fromLinkable: true,
                  toLinkable: true
              },
              new go.Binding("text", "fromText")),

          $(go.TextBlock, // the "to" label
              {
                  textAlign: "center",
                  font: "bold 12px sans-serif",
                  stroke: "#1967B3",
                  segmentIndex: -1,
                  segmentOffset: new go.Point(NaN, NaN),
                  segmentOrientation: go.Link.OrientUpright,
                  fromLinkable: true,
                  toLinkable: true
              },
              new go.Binding("text", "toText"))
      );

  myDiagram.model = $(go.GraphLinksModel, {
      copiesArrays: true,
      copiesArrayObjects: true,
      linkFromPortIdProperty: "fromPort",
      linkToPortIdProperty: "toPort",
      nodeDataArray: nodeDataArray,
      linkDataArray: linkDataArray
  });
}


function loadFilterByRelationType() {
  var json = linkDataArray;
  var appended = [];
  $.each(json, function(i, v) {
      // check if doesn't exist in the array
      if ($.inArray(this.type, appended) == -1) {
          // append
          appended.push(this.type)
          $("#filter-by-relation-type").append($("<div class='text-sm'>").text(this.type).prepend(
              $("<input>").attr({
                  'type': 'checkbox',
                  'checked': true,
                  'class': 'input-relation-type-checkbox',
                  'name': 'subscribe-relation-type',
                  "data-feed": this.type
              }).val(this.type)
              .prop('checked', this.checked)
          ));
      }
  });
  $(".input-relation-type-checkbox").on('change', function() {
      setCheckboxesForTableNames()
      setCheckboxesForRelationTypes()
  });
}

function setCheckboxesForRelationTypes() {
  newLinkDataArray = []

  $(".input-relation-type-checkbox").each(function() {
      if ($(this).prop('checked')) {
          console.log($(this).val())
          for (let i = 0; i < linkDataArray.length; i++) {
              const element = linkDataArray[i];
              if (element.type == $(this).val()) {
                  newLinkDataArray.push(element)
              }
          }
      }
  });
  myDiagram.model.linkDataArray = newLinkDataArray
}

function loadFilterByTableNames() {
  var json = nodeDataArray;
  var appended = [];
  $.each(json, function(i, v) {
      // check if doesn't exist in the array
      if ($.inArray(this.key, appended) == -1) {
          // append
          appended.push(this.key)
          $("#filter-by-table-name").append($("<div class='text-sm'>").text(this.key).prepend(
              $("<input>").attr({
                  'type': 'checkbox',
                  'checked': true,
                  'class': 'input-table-name-checkbox',
                  'name': 'subscribe-table-name',
                  "data-feed": this.key
              }).val(this.key)
              .prop('checked', this.checked)
          ));
      }
  });

  $(".input-table-name-checkbox").on('change', function() {
      setCheckboxesForTableNames();
      setCheckboxesForRelationTypes();
  });
}


$("#input-relation-type-checkbox-check-all").on('change', function() {
  $(".input-relation-type-checkbox").prop('checked', this.checked);
  setCheckboxesForTableNames();
  setCheckboxesForRelationTypes();
});

$("#input-table-names-checkbox-check-all").on('change', function() {
  $(".input-table-name-checkbox").prop('checked', this.checked);
  setCheckboxesForTableNames();
  setCheckboxesForRelationTypes();
});

function setCheckboxesForTableNames() {
  newNodeDataArray = []
  newLinkDataArray = []

  $(".input-table-name-checkbox").each(function() {
      if ($(this).prop('checked')) {

          for (let i = 0; i < nodeDataArray.length; i++) {
              const element = nodeDataArray[i];
              if (element.key == $(this).val()) {
                  newNodeDataArray.push(element)
              }
          }
          for (let i = 0; i < linkDataArray.length; i++) {
              const element = linkDataArray[i];
              if (element.from == $(this).val() || element.to == $(this).val()) {
                  newLinkDataArray.push(element)
              }
          }
      }
  });
  myDiagram.model.nodeDataArray = newNodeDataArray
  myDiagram.model.linkDataArray = newLinkDataArray
}

function fetchERDData() {
  fetch('erd.json')
      .then(
          response => {
              response.json().then(function(data) {
                  nodeDataArray = data.node_data
                  linkDataArray = data.link_data
                  loadFilterByTableNames()
                  loadFilterByRelationType()
                  setCheckboxesForTableNames();
                  setCheckboxesForRelationTypes();
              });
          }).catch(err => console.log('Fetch Error :-S', err));
}
fetchERDData();

window.addEventListener('DOMContentLoaded', init);
