const afterDOMInitiated = ( cb, delay = 0 ) => {
	if ( /comp|inter|loaded/.test( document.readyState ) ) {
		setTimeout( cb, delay );
	} else {
		document.addEventListener('DOMContentLoaded', () => {
            setTimeout(cb, delay);
        }, false);
	}
};
( function (w, d) {
	const peFunnelkitSync = {
		/**
		 * Log messages to debug.
		 * 
		 * @param {string} message 
		 */
		_log( message ) {
			console.log( message );
		},
		/**
		 * Bind Actions
		 * 
		 * @since 1.0.0
		 */
		bind() {
			peFunnelkitSync.syncSubscriberID();
		},
		/**
		 * Checks if FK Automation plugin is active.
		 */
		isFunnelKitAutomationsActive: function() {
			return 'object' === typeof bwfanParamspublic;
		},
		/**
		 * get PushEngage subscriber ID from API.
		 */
		getPeSubscriberID: async function() {
			try{
				const subscriberID = await window.PushEngage.getSubscriberId();
				return subscriberID;
			} catch (error) {
				console.log(error);
			}
			return null;
		},
		/**
		 * Initialize.
		 * 
		 * @since 1.0.0
		 */
		init() {
			peFunnelkitSync.bind();
		},
		/**
		 * Create cookie.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} name  Cookie name.
		 * @param {string} value Cookie value.
		 * @param {string} days  Whether it should expire and when.
		 */
		createCookie: function( name, value, days ) {

			var expires = '';
			var secure = '';

			if ( peSyncData.is_ssl ) {
				secure = ';secure';
			}

			// If we have a days value, set it in the expiry of the cookie.
			if ( days ) {

				// If -1 is our value, set a session-based cookie instead of a persistent cookie.
				if ( '-1' === days ) {
					expires = '';
				} else {
					var date = new Date();
					date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
					expires = ';expires=' + date.toGMTString();
				}
			} else {
				expires = ';expires=Thu, 01 Jan 1970 00:00:01 GMT';
			}

			// Write the cookie.
			document.cookie = name + '=' + value + expires + ';path=/;samesite=strict' + secure;
		},
		/**
		 * Retrieve cookie.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} name Cookie name.
		 *
		 * @returns {string|null} Cookie value or null when it doesn't exist.
		 */
		getCookie: function( name ) {
			var nameEQ = name + '=',
				ca     = document.cookie.split( ';' );

			for ( var i = 0; i < ca.length; i++ ) {
				var c = ca[i];
				while ( ' ' === c.charAt( 0 ) ) {
					c = c.substring( 1, c.length );
				}
				if ( 0 === c.indexOf( nameEQ ) ) {
					return c.substring( nameEQ.length, c.length );
				}
			}

			return null;
		},
		/**
		 * Sync Subscriber ID to contact.
		 * @returns null
		 */
		syncSubscriberID: async function() {
			var contactUID   = peFunnelkitSync.getCookie('_fk_contact_uid');
			var subscriberID = await peFunnelkitSync.getPeSubscriberID();

			// if ( ! peFunnelkitSync.isFunnelKitAutomationsActive() ) {
			// 	return null;
			// }

			if ( null === subscriberID ) {
				return subscriberID;
			}

			if ( null !== contactUID ) {
				let syncedSubscriberIDs = peFunnelkitSync.getCookie('_pe_fk_synced_sids');

				if ( null !== syncedSubscriberIDs ) {
					// Check if syncedSubscriberIDs is an array and subscriberID is already present in the array.
					let subscriberIDsArray = JSON.parse(syncedSubscriberIDs);

					if ( subscriberIDsArray.includes(subscriberID) ) {
						return null;
					}

				}
				// send a AJAX request passing contactUID and subscriberID as post items.
				var formData = new FormData();

				formData.append( 'nonce', peSyncData?.nonce );
				formData.append( 'action', 'update_funnelkit_contact' );
				formData.append( 'contactID', contactUID );
				formData.append( 'subscriberID', subscriberID );

				fetch(
					peSyncData.ajax_url,
					{
						method: 'POST',
						cache: 'no-cache',
						credentials: 'same-origin',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
							'Cache-Control': 'no-cache',
						},
						body: new URLSearchParams( formData ),
					},
				)
				.then( res => res.json() )
				.then(
					( result ) => {
						if (result.success) {
							peFunnelkitSync.createCookie( '_pe_fk_synced_sids', JSON.stringify( result.data.subscriber_tokens ), 365 );
						}
					},
					( error ) => {

					},
				);

				return null;
			}
		}
	};

	afterDOMInitiated( () => {
		peFunnelkitSync.init();
	}, 500 );
}(window, document) );