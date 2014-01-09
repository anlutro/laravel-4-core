#!/usr/bin/env python3.3

"""L4 Scaffolding

Usage:
  l4s.py <name> <path> [<namespace>]
"""

from docopt import docopt
from os.path import realpath, dirname, basename, exists, join
from os import listdir

def replace_filename(path, name):
	return path.replace('_NAME_', name)

def copyfile(src, target, name, namespace):
	srcfile = open(src, 'r')
	trgfile = open(target, 'w+')

	for line in srcfile:
		line = line.replace('_NAME_', name)
		line = line.replace('_NAMESPACE_', namespace)
		# print(line)
		trgfile.write(line)

	srcfile.close()
	trgfile.close()

def scaffold(name, target_dir, namespace='MyName\MyApp'):
	skel_path = dirname(__file__) + '/skel'

	for f in listdir(skel_path):
		src_path = realpath(join(skel_path, f))

		filename = replace_filename(basename(f), name)
		target_path = target_dir + '/' + filename
		
		if (exists(target_path)):
			print(filename, 'already exists, skipping')
		else:
			print('Copying', src_path, '->', target_path, '...')
			copyfile(src_path, target_path, name, namespace)

def main():
	args = docopt(__doc__)
	name, path = args['<name>'], realpath(args['<path>'])
	namespace = args['<namespace>']
	
	print('')
	print('Resource name:', name)
	if namespace: print('Namespace:', namespace)
	print('Target directory:', path)
	print('')

	scaffold(name, path, namespace)

	print('')
	print('Done!')
	print('')

if __name__ == '__main__':
	main()