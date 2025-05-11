const gridOptions = {
	rowData: [],
	columnDefs: [
		{ field: 'id', width: 100, cellStyle: { textAlign: 'center' } },
		{ field: 'userId', width: 100, cellStyle: { textAlign: 'center' } },
		{ field: 'title', flex: 1 },
		{ field: 'completed', width: 200, cellStyle: { textAlign: 'center' } },
	],
	defaultColDef: {
		filter: true,
		editable: true,
	},
	pagination: true,
	paginationPageSize: 5,
	paginationPageSizeSelector: [5, 10, 50, 100, 200, 500, 1000],
	rowSelection: {
		mode: 'multiRow',
		enableClickSelection: true,
	},
	selectionColumnDef: {
	},
};

let gridApi = agGrid.createGrid(document.querySelector('#myGrid'), gridOptions);

fetch('https://jsonplaceholder.typicode.com/todos')
	.then((response) => response.json())
	.then((data) => gridApi.setGridOption('rowData', data));


