import { statSync } from 'fs';
import { readdir, readFile } from 'fs/promises';
import { writeFile } from 'fs/promises';
import { join as joinPath } from 'path';
import process from 'process';

const repository = JSON.parse( process.argv[2] );
const skip_dirs = [ '.github', '.git' ];

/**
 * @param {string} dirPath
 * @param {(filePath: string) => Promise<void>} callback
 */
const traverseDirectory = async ( dirPath, callback ) => {
	if ( skip_dirs.includes( dirPath ) ) {
		console.log( 'Skipping %s', dirPath );
		return;
	}
	console.log( 'Traversing %s', dirPath );

	const files = await readdir( dirPath ); // Read the contents of the directory
	for ( const file of files ) {
		const filePath = joinPath( dirPath, file );

		if ( statSync( filePath ).isFile() ) {
			await callback( filePath );
		} else { // Recursively traverse directories
			await traverseDirectory( filePath, callback );
		}
	}
};

/**
 * Implementation of php's `ucwords()` method.
 *
 * Based on https://gist.github.com/rickycheers/4541395
 *
 * @returns string
 */
String.prototype.ucwords = function() {
	const str = this.toLowerCase();
	return str.replace(
		/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
		function(s){
			return s.toLocaleUpperCase();
		}
	);
};

/**
 * Build a template using envs
 * @param {string} filePath
 */
const buildTemplate = async ( filePath ) => {
	console.log( 'Building %s', filePath );

	const templateFile   = await readFile( filePath, 'utf-8' );
	let renderedTemplate = templateFile, replacements;

	if ( 'README.md' === filePath ) {
		replacements = {
			'EXAMPLE_REPO_NAME': repository.name,
			'EXAMPLE_REPO_DESCRIPTION': repository.description ?? 'A spiffy new theme.',
			'EXAMPLE_REPO_PLAYGROUND_URL': 'https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/' + repository.full_name + '/refs/heads/main/playground.json'
		};
	} else {
		replacements = {
			'Theme Repo Template': ( String(repository.name).replace( /[\W_]+/g, ' ' ).ucwords() ),
			'ThemeRepoTemplate': ( String(repository.name).replace( /[\W_]+/g, ' ' ).ucwords().replace( ' ', '' ) ),
			'theme-repo-template': repository.name.toLowerCase(),
			'A starter theme for FSE.  Will generally be overwritten.': repository.description ?? 'A spiffy new theme.',
			'georgestephanis/theme-repo-template': repository.full_name,
		};
	}

	for ( const [ key, value ] of Object.entries( replacements ) ) {
		renderedTemplate = renderedTemplate.replaceAll( key, value );
	}

	if ( renderedTemplate !== templateFile ) {
		console.log( 'Changes were made. Overwriting file.' );
		await writeFile( filePath, renderedTemplate );
	}
};

await traverseDirectory( '.', buildTemplate );
