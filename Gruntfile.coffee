module.exports = (grunt) ->
	grunt.initConfig
		pkg: grunt.file.readJSON("package.json")
		directory:
			components: "Resources/Private/BowerComponents"
			build: "Resources/Public/Build"
			source: "Resources/Private/Assets"

	############################ Assets ############################

	##
	# Assets: clean up environment
	##
		clean:
			temporary:
				src: [".tmp"]


	##
	# Assets: copy some files to the distribution dir
	##
		copy:
			gif:
				files: [
					# includes files within path
					expand: true
					flatten: true
					src: "<%= directory.components %>/fine-uploader/_dist/jquery.fineuploader-*/*.gif"
					dest: "<%= directory.build %>"
					filter: "isFile"
				]

	############################ StyleSheets ############################

	##
	# StyleSheet: minification of CSS
	##
		cssmin:
			options: {}
			css:
				files: [
					src: "<%= sass.css.files[0].dest %>"
					dest: ".tmp/cssmin/main.min.css"
				]

	##
	# StyleSheet: compiling to CSS
	##
		sass:
			css:
				options:
				# output_style = expanded or nested or compact or compressed
					style: "expanded"
					sourcemap: 'none'

				files: [
					src: "<%= directory.source %>/StyleSheets/Sass/main.scss"
					dest: ".tmp/sass/main.css"
				]

	############################ JavaScript ############################

	##
	# JavaScript: check javascript coding guide lines
	##
		jshint:
			files: [
				"<%= directory.source %>/JavaScript/*.js"
			]

			options:
			# options here to override JSHint defaults
				curly: true
				eqeqeq: true
				immed: true
				latedef: true
				newcap: true
				noarg: true
				sub: true
				undef: true
				boss: true
				eqnull: true
				browser: true
				loopfunc: true
				globals:
					jQuery: true
					console: true
					define: true
					alert: true
					MediaUpload: true

	##
	# JavaScript: minimize javascript
	##
		uglify:
			js:
				files: [
					src: "<%= jshint.files %>"
					dest: ".tmp/uglify/MediaUpload.min.js"
				]

	########## concat css + js ############
		concat:
			options:
				separator: "\n\n"
			js:
				src: [
					"<%= directory.components %>/fine-uploader/dist/jquery.fine-uploader.js"
					"<%= jshint.files %>"
				]
				dest: "<%= directory.build %>/media_upload.js"
			js_min:
				src: [
					"<%= directory.components %>/fine-uploader/dist/jquery.fine-uploader.min.js"
					"<%= uglify.js.files[0].dest %>"
				]
				dest: "<%= directory.build %>/media_upload.min.js"
			css:
				src: [
					"<%= directory.components %>/fine-uploader/dist/fine-uploader.css"
					"<%= sass.css.files[0].dest %>"
				]
				dest: "<%= directory.build %>/media_upload.css"
			css_min:
				src: [
					"<%= directory.components %>/fine-uploader/dist/fine-uploader.min.css"
					"<%= cssmin.css.files[0].dest %>"
				]
				dest: "<%= directory.build %>/media_upload.min.css"

	########## Watcher ############
		watch:
			css:
				files: [
					"<%= directory.source %>/StyleSheets/**/*.scss"
				]
				tasks: ["build-css"]
			js:
				files: ["<%= jshint.files %>"]
				tasks: ["build-js"]


	########## Help ############
	grunt.registerTask "help", "Just display some helping output.", () ->
		grunt.log.writeln "Usage:"
		grunt.log.writeln ""
		grunt.log.writeln "- grunt watch        : watch your file and compile as you edit"
		grunt.log.writeln "- grunt build        : build your assets ready to be deployed"
		grunt.log.writeln "- grunt build-css    : only build your css files"
		grunt.log.writeln "- grunt build-js     : only build your js files"
		grunt.log.writeln "- grunt build-icons  : only build icons"
		grunt.log.writeln "- grunt clean        : clean behind you the temporary files"
		grunt.log.writeln ""
		grunt.log.writeln "Use grunt --help for a more verbose description of this grunt."
		return

	# Load Node module
	grunt.loadNpmTasks "grunt-contrib-uglify"
	grunt.loadNpmTasks "grunt-contrib-jshint"
	grunt.loadNpmTasks "grunt-contrib-watch"
	grunt.loadNpmTasks "grunt-contrib-concat"
	grunt.loadNpmTasks "grunt-contrib-sass";
	grunt.loadNpmTasks "grunt-contrib-cssmin"
	grunt.loadNpmTasks "grunt-contrib-copy"
	grunt.loadNpmTasks "grunt-contrib-clean"
	grunt.loadNpmTasks "grunt-string-replace"
	grunt.loadNpmTasks "grunt-imagine"
	grunt.loadNpmTasks "grunt-pngmin"

	# Alias tasks
	grunt.task.renameTask("string-replace", "replace")

	# Tasks
	grunt.registerTask "build", ["build-js", "build-css", "build-icons"]
	grunt.registerTask "build-js", ["jshint", "uglify", "concat:js", "concat:js_min"]
	grunt.registerTask "build-css", ["sass", "cssmin", "concat:css", "concat:css_min"]
	grunt.registerTask "build-icons", ["copy"]
	grunt.registerTask "default", ["help"]
	return