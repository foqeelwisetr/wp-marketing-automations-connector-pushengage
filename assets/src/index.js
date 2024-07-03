import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import './style.scss';

import NotificationsTabContent from './notificationsTab';

addFilter( 'bwfanAddTabOnSingleContact', 'bwfan', ( tabList ) => {
	tabList.push( {
		key: 'pe-notifications',
		name: __( 'Push Notifications', 'wp-marketing-automations-crm' ),
	} );
	return tabList;
} );

addFilter( 'bwfanAddSingleContactCustomTabData', 'bwfan', ( data, tab ) => {
	if ( tab === 'pe-notifications' ) {
		data = NotificationsTabContent;
	}
	return data;
} );

addFilter( 'bwfanAddContactInsightTag', 'bwfan', ( tags, contact ) => {
	tags.push( 'Custom tag' );
	return tags;
} );

addFilter(
	'bwfanEmptyComponentFilter',
	'bwfan',
	( data, query ) => {
		if (
			query.hasOwnProperty( 'path' ) &&
			query.path.split( '/' ).hasOwnProperty( 1 ) &&
			query.path.split( '/' )[ 1 ] === 'custom-url'
		) {
			if (
				query.path.split( '/' ).hasOwnProperty( 2 ) &&
				query.path.split( '/' )[ 2 ] === 'list'
			) {
				data = <>Here is Custom list page content</>;
			} else {
				data = <>Here is Custom overview page content</>;
			}
			return data;
		}
	},
	10
);

addFilter(
	'bwfanSet404ComponentMenuFilter',
	'bwfan',
	( data, query ) => {
		if (
			query.hasOwnProperty( 'path' ) &&
			query.path.split( '/' ).hasOwnProperty( 1 ) &&
			query.path.split( '/' )[ 1 ] === 'custom-url'
		) {
			let secondActive = false;

			if (
				query.path.split( '/' ).hasOwnProperty( 2 ) &&
				query.path.split( '/' )[ 2 ] === 'list'
			) {
				secondActive = true;
			}

			data = {
				pageHeader: 'Custom Page',
				leftNav: 'custom_url',
				rightNav: secondActive ? 'customlist' : 'custom',
				l2NavAlign: 'left',
				l2NavType: 'menu',
				l2Nav: {
					custom: {
						name: 'Overview',
						link: 'admin.php?page=autonami&path=/custom-url',
					},
					customlist: {
						name: 'Custom List',
						link: 'admin.php?page=autonami&path=/custom-url/list',
					},
				},
			};
		}
		return data;
	},
	1
);

const newSectionData = () => (
	<div className="bwf-sc-data-section" key={ 1 }>
		<div className="bwf-sc-section-heading">
			{ __(
				'Notifications',
				'wp-marketing-automations-crm'
			) }
		</div>

		<div className={ 'bwf-sc-section-row' }>
			<div className="bwf-sc-side-content-item">
				<div className="bwf-side-text-light">
					This is Custom label1
				</div>
				<div className="bwf-side-text-dark">
					This is Custom value1
				</div>
			</div>
			<div className="bwf-sc-side-content-item">
				<div className="bwf-side-text-light">
					This is Custom label1
				</div>
				<div className="bwf-side-text-dark">
					This is Custom value1
				</div>
			</div>
			<div className="bwf-sc-side-content-item">
				<div className="bwf-side-text-light">
					This is Custom label1
				</div>
				<div className="bwf-side-text-dark">
					This is Custom value1
				</div>
			</div>
		</div>
	</div>
);

addFilter(
	'bwfanAddAfterInsightSection',
	'bwfan',
	( data, contact ) => {
		data.push( {
			key: 'custom',
			data: newSectionData(),
			priority: 40,
		} );
		return data;
	},
	10
);