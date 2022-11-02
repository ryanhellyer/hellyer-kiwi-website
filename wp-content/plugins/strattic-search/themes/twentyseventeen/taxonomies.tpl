<p>
	<label for="strattic-{{taxonomy}}">{{taxonomy_label}}</label>
	<select data-taxonomy="{{taxonomy}}">
		<option value="">Select a {{taxonomy}}</option>';
		{{#options}}
		<option value="{{term_id}}">{{term_name}}</option>
		{{/options}}
	</select>
</p>