'use strict';

const e = React.createElement;

class Header extends React.Component {

	constructor(props) {
		super(props);
		this.state = { liked: false };
		this.config = window.config.header;
	}

	render() {
		return e(
			'Header',
			{ onClick: () => this.setState({ liked: true }) },
			'<img src=" ' + this.config.logo + '"/><h1>' + this.config.title + '</h1>'
		);
	}
}

const domContainer = document.querySelector('#header');
ReactDOM.render(e(Header), domContainer);

